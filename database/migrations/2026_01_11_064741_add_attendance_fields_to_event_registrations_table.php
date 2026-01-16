<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('attendance_status')->nullable()->after('joined_at'); // present/absent/late
            $table->unsignedTinyInteger('task_completion')->default(0)->after('attendance_status'); // 0-100
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['attendance_status', 'task_completion']);
        });
    }
};
