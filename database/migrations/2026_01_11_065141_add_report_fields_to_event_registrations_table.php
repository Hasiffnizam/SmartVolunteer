<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {

            // ✅ Add ONLY if not exists (manual safe approach)
            if (!Schema::hasColumn('event_registrations', 'task_completion')) {
                $table->unsignedTinyInteger('task_completion')->default(0)->after('attendance_status');
            }

            if (!Schema::hasColumn('event_registrations', 'note')) {
                $table->string('note', 255)->nullable()->after('task_completion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('event_registrations', 'task_completion')) {
                $table->dropColumn('task_completion');
            }
            if (Schema::hasColumn('event_registrations', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
