# 🎓 Career 180 — Scalable Mini-LMS

![Tests](https://github.com/bola-cloud/task-lms/actions/workflows/tests.yml/badge.svg)
![Linter](https://github.com/bola-cloud/task-lms/actions/workflows/lint.yml/badge.svg)

A production-ready Learning Management System built for the Career 180 Hiring Quest.
Built with **Laravel 12**, **Livewire v3**, **Alpine.js**, **Filament v3**, **Plyr.js**, and **Docker**.

---

## 💎 Senior Engineering & Clean Code

This project goes beyond the basic requirements to showcase senior-level architectural decisions:

- **Action Pattern (`app/Actions/`)**: All business logic (Enrollment, Completion, Certification) is encapsulated in strictly-typed, single-responsibility Action classes.
- **#[Computed] Properties**: Livewire components are optimized using `#[Computed]` attributes for all derived data (courses, lessons, progress), ensuring minimal database round-trips and clean state management.
- **Strict PHP 8.2+ Typing**: Every method and action uses strict return types and parameter hinting, backed by comprehensive docblocks.
- **Event-Driven Resilience**: Course completion isn't a bulky controller logic; it's a reactive chain of Events (`LessonCompleted`) and Queued Listeners (`IssueCertificate`, `SendEmail`).
- **RBAC Security**: Implemented the `FilamentUser` contract and `is_admin` role-based access to strictly isolate the administrative panel from students.

---

## ✅ Spec Compliance Summary

| Requirement | Status |
|---|---|
| Public home `/` — published courses, no N+1 | ✅ Optimized with `withCount()` and eager loading |
| Registration → queued Welcome Email | ✅ |
| Enrollment requires auth, no duplicates, no drafts | ✅ |
| Concurrency: rapid clicks / retries / queue retries | ✅ `lockForUpdate()` + DB unique constraints |
| `/courses/{slug}` — details, ordered lessons, free preview | ✅ |
| Unique slug + soft delete isolation | ✅ |
| `/courses/{slug}/lessons/{lesson}` — Plyr.js player | ✅ |
| `started_at` / `completed_at` tracking | ✅ |
| Completion = all required lessons done | ✅ |
| Certificate (UUID) on completion | ✅ |
| Completion email — async, exactly once | ✅ Idempotent listeners |
| Course edits stay logically consistent post-enrollment | ✅ Dynamic progress recalculation |
| Alpine accordion (curriculum list) | ✅ |
| Alpine confirmation modal (mark complete) | ✅ |
| Alpine animated progress bar | ✅ |
| Alpine Plyr lifecycle integration | ✅ Standardized Alpine instance |
| Performance Indexes | ✅ Covered slug, order, and foreign keys |

---

## 🚀 Quick Start (Docker — Clean Machine)

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running

### 1. Clone and configure
```bash
git clone <your-repo-url> career180-lms
cd career180-lms

# Copy environment file
cp .env.example .env
```

### 2. Start all containers
```bash
docker compose up --build -d
```
> This starts **5 services**: PHP-FPM app, Nginx, MySQL 8, Redis, and a dedicated Queue Worker.
> The `app` container waits for MySQL and Redis to pass health checks before starting.

### 3. Bootstrap the application
```bash
# Linux / Mac / WSL / Git Bash
chmod +x scripts/setup.sh
./scripts/setup.sh
```

**What the script does, in order:**
1. Installs Composer dependencies inside the container
2. Generates the `APP_KEY`
3. Runs all migrations (Standardized English schema)
4. Seeds the database (LMS content + Users)
5. Builds Vite assets (Tailwind CSS)
6. Runs the full Pest test suite (45 Passing)

### 4. Access the application

| Interface | URL | Credentials |
|---|---|---|
| 🏠 Public LMS | http://localhost:8080 | (guest or register) |
| 🎓 Learner Account | http://localhost:8080/login | `learner@career180.com` / `password` |
| ⚙️ Admin Panel (Filament) | http://localhost:8080/admin | `admin@career180.com` / `password` |

---

## 🧪 Running Tests

```bash
# Via Docker (recommended)
docker compose exec app php artisan test

# Locally
php artisan test
```

**Test coverage includes (45 tests):**
- **Concurrency**: Race condition prevention for enrollment.
- **Idempotency**: Certificates and emails sent exactly once.
- **Isolation**: Policies preventing unauthorized lesson access.
- **Edge Cases**: Slug conflicts with soft-deletes and post-enrollment content changes.

---

## 🐳 Docker Services

| Service | Image | Role |
|---|---|---|
| `app` | PHP 8.3-FPM (custom) | Laravel application |
| `web` | `nginx:alpine` | Reverse proxy / static assets |
| `db` | `mysql:8.0` | Primary database |
| `worker` | PHP 8.3-FPM (custom) | Queue worker: emails + certificates |
| `redis` | `redis:alpine` | Queue backend + cache |

Health checks are configured on `db` and `redis` — the `app` and `worker` containers wait for them before starting.

---

## 🏗️ Architecture Highlights

### Event-Driven Completion Flow
```
CompleteLessonAction
    └── fires: LessonCompleted
            └── CheckCourseCompletion (listener)
                    └── if all required lessons done: fires CourseCompleted
                            ├── IssueCertificate (queued listener)
                            └── SendCourseCompletionEmail (queued listener)
```

### Data Integrity
- **Unique constraint** on `(user_id, course_id)` in `enrollments`
- **Unique constraint** on `(user_id, course_id)` in `certificates`
- **DB transaction + `lockForUpdate()`** in enrollment
- **`firstOrCreate()` atomics** for idempotent certificate generation

---

## 📁 Key File Structure

```
app/
├── Actions/        # Business Logic (Enroll, Complete, Certify)
├── Events/         # Domain Events (Lesson/Course Completed)
├── Listeners/      # Responders (Check Progress, Send Mail)
├── Livewire/       # UI Components (Optimized #[Computed])
├── Models/         # Data Layer (SoftDeletes, UUIDs)
└── Policies/       # Authorization (Isolation)

docs/
├── ARCHITECTURE.md # ERD + Concurrency Strategy
└── PRODUCT_THINKING.md # Business Risks & Evolution
```

---

## 📚 Documentation

- **[Architecture & ERD](docs/ARCHITECTURE.md)** — Entity relationships, concurrency strategy, and design patterns
- **[Product Thinking](docs/PRODUCT_THINKING.md)** — Business risks, key metrics, future evolution, and trade-off reasoning

---

## 🎬 Project Showcase

*This section is reserved for the live demonstration video, covering the technical architecture, concurrency strategies, and the integrated Docker environment.*

---

*Developed for the Career 180 Senior Full-Stack Laravel Engineer Hiring Quest.*
