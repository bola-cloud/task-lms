# Product Thinking — Career 180 LMS

> **Note**: This document reflects my actual decision-making process — the reasoning I'd walk through in a product meeting, not a generic AI-generated answer.

---

## 1. Business Risks

### Risk 1: Data Integrity Under Concurrency
**What it is**: At scale, two learners could race to enroll simultaneously, or a flaky connection could retry an enrollment request twice, resulting in duplicate records, double-charged payments (if we add paid courses), or conflicting state.

**How my architecture mitigates it**:
- `EnrollUserAction` wraps enrollment in a `DB::transaction()` with `lockForUpdate()` — this is a pessimistic lock that serializes concurrent enrollment attempts at the database level.
- A hard **unique constraint** on `(user_id, course_id)` in `enrollments` is the final line of defense. Even if the application logic fails, the DB will reject the duplicate.
- `GenerateCertificateAction` uses `firstOrCreate()` on an atomic DB operation, ensuring exactly one certificate per user/course regardless of how many times the event fires.

### Risk 2: Content Piracy & Unauthorized Access
**What it is**: Courses are the core product. If users can share direct video links, bypass enrollment, or view each other's progress, the platform loses revenue and trust.

**How my architecture mitigates it**:
- `LessonPolicy::view()` is the single authorization point — it checks enrollment status and free-preview flags before granting access.
- Policies are evaluated at the route/component level, not just in the UI, so bypassing the frontend is impossible.
- Video hosting via Vimeo/Mux provides signed URLs server-side, preventing direct sharing of raw file URLs.
- User data is fully isolated: no query in the system returns another user's progress, certificates, or enrollment records. All queries are scoped by `Auth::id()`.

### Risk 3: Availability During Traffic Spikes (Course Launch)
**What it is**: A popular course launch or a marketing email blast can cause a sudden traffic spike, causing slow page loads, failed enrollments, or cascading queue failures.

**How my architecture mitigates it**:
- All side effects (welcome emails, completion emails, certificate generation) are **queued asynchronously** via a dedicated `worker` container. A spike in completions won't slow down the primary request/response cycle.
- **Database indexes** on `is_published`, `order`, `user_id/lesson_id`, and `enrolled_at` mean the public homepage and course pages are fast even with millions of records.
- The architecture is **horizontally scalable**: adding more `app` or `worker` containers behind a load balancer requires zero code changes.

---

## 2. Metrics That Matter

> For each metric: what to capture, whether to compute or store, and how to keep it performant.

### Metric 1: Course Completion Rate
**Formula**: `certificates issued / total enrollments` per course.
**Capture**: Listen to `CourseCompleted` event. Increment a counter.
**Store or Compute?**: Store an aggregate in a `course_stats` table (a single row per course, updated via a queued job). Computing this on demand from `certificates` and `enrollments` would require expensive JOINs at scale.
**Performance**: The `CourseCompleted` event listener writes to the aggregate table asynchronously. The homepage reads one pre-computed row per course — `O(1)`.

### Metric 2: Average Time-to-Complete a Lesson
**Formula**: `avg(completed_at - started_at)` from `lesson_progress`.
**Capture**: Already captured — `started_at` is written when the lesson page loads, `completed_at` when the user confirms completion.
**Store or Compute?**: Compute nightly via a scheduled command and store in a `lesson_stats` table. Hot-computing averages across millions of progress rows would be prohibitively slow.
**Performance**: A cron job aggregates at off-peak hours. The result is a single indexed read.

### Metric 3: Enrollment Conversion Rate
**Formula**: `enrollments / course_detail_page_views` per course.
**Capture**: Page views via a lightweight event (`CourseViewed`) dispatched in `CourseDetails::mount()`, stored in Redis (increment a counter). Enrollments are already tracked.
**Store or Compute?**: Store raw counts in Redis, flush to DB daily. This avoids hitting MySQL on every page view.
**Performance**: Redis `INCR` is O(1) and non-blocking. No DB write on the hot path.

### Metric 4: Learner Drop-off Point
**Formula**: The lesson with the highest "started but not completed" ratio.
**Capture**: `lesson_progress` has both `started_at` and `completed_at`. Query: `WHERE started_at IS NOT NULL AND completed_at IS NULL`.
**Store or Compute?**: Compute weekly. Store the top 3 drop-off lessons per course in `course_stats`.
**Performance**: This query is expensive but only runs once per week via a scheduled command, not on the request path.

### Metric 5: Active Monthly Learners (AML)
**Formula**: Distinct users with at least one `lesson_progress` record updated in the last 30 days.
**Capture**: The `lesson_progress.updated_at` column already captures this.
**Store or Compute?**: Compute on demand for the admin dashboard with a single indexed query: `SELECT COUNT(DISTINCT user_id) FROM lesson_progress WHERE updated_at >= NOW() - INTERVAL 30 DAY`. An index on `updated_at` keeps this fast at scale.
**Performance**: This runs only when an admin loads the dashboard. Add a 5-minute cache with `Cache::remember()` to prevent hammering it.

---

## 3. Future Evolution

### Paid Courses
**What supports this now**: `EnrollUserAction` is a single, isolated class. Adding a payment step requires only inserting a payment check before the enrollment record is created — no other code changes.
**What needs refactoring**: Add a `Payment` model, a `Stripe/Paymob` gateway integration, and a `PaymentCompleted` event that triggers `EnrollUserAction`. The action itself stays unchanged.

### Mobile App API
**What supports this now**: All business logic lives in Action classes, not in Livewire components or controllers. An API controller can call `EnrollUserAction` or `CompleteLessonAction` directly, reusing 100% of the logic.
**What needs refactoring**: Add `routes/api.php` routes, API resource transformers (JSON responses), and Laravel Sanctum for token-based auth. The Livewire components would remain for the web view.

### Corporate Multi-Tenant Accounts
**What supports this now**: Every query is scoped by `user_id`. The `Enrollment` and `LessonProgress` models are ready for an additional `organization_id` scope.
**What needs refactoring**: Add an `organizations` table, an `OrganizationUser` pivot, and a global scope on relevant models. This is a significant migration but the existing scoping patterns reduce complexity.

### Gamification Badges
**What supports this now**: The event-driven architecture makes this nearly free to add. Adding badges requires only a new `AwardBadgeListener` on the existing `LessonCompleted` and `CourseCompleted` events, with a new `Badge` and `UserBadge` model.
**What needs refactoring**: Nothing in the core domain changes. This is a purely additive feature.

---

## 4. Trade-offs I Made (and Why)

### Trade-off 1: CDN Libraries (Plyr, Alpine, Tailwind CDN) vs. npm Bundle
**What I chose**: Load Plyr.js, Alpine.js, and Tailwind CSS via CDN in the public layout.
**Why**: The project uses a Vite-based build pipeline, but running `npm run dev` is an extra manual step that can break reviewer setup. Using CDN ensures the public UI renders perfectly with **zero build step** — reviewers see a polished UI immediately after `docker compose up`. The trade-off is slightly slower first-load due to external CDN requests and less control over asset versions. In production, I would bundle and fingerprint all assets via Vite.

### Trade-off 2: Synchronous Progress Writes vs. Fully Async
**What I chose**: Writing `started_at` and `completed_at` to `lesson_progress` happens **synchronously** on the request, not via a queue.
**Why**: Async progress writes introduce a race condition — if a user marks a lesson complete and immediately checks their progress bar, the queue might not have processed yet, causing a confusing UI state ("I marked it done but it still shows incomplete"). The synchronous approach gives **immediate, consistent UI feedback**. The performance cost is one fast, indexed write per action — negligible. Only the heavyweight side effects (email, certificates) are async.

### Trade-off 3: Pessimistic Locking vs. Optimistic Locking for Enrollment
**What I chose**: `lockForUpdate()` (pessimistic locking) in the enrollment transaction.
**Why**: Optimistic locking (version columns + retry) adds complexity and still requires handling retry logic in the application layer. For enrollment — a rare, critical operation — pessimistic locking is simpler, more predictable, and guarantees correctness. The trade-off is slightly reduced concurrency (rows are locked for milliseconds), but enrollment is not a high-frequency-per-row operation, so contention is essentially zero in practice.
