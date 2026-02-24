<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->index('is_published');
            $table->index('created_at');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->index(['course_id', 'order']);
            $table->index('is_free_preview');
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->index(['user_id', 'completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['is_published']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex(['course_id', 'order']);
            $table->dropIndex(['is_free_preview']);
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'completed_at']);
        });
    }
};
