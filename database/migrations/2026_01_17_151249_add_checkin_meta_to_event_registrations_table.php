<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('event_registrations', function (Blueprint $table) {
      if (!Schema::hasColumn('event_registrations', 'checked_in_at')) {
        $table->timestamp('checked_in_at')->nullable()->after('attendance_status');
      }
      if (!Schema::hasColumn('event_registrations', 'check_in_method')) {
        $table->string('check_in_method', 20)->nullable()->after('checked_in_at'); // email | qr | admin
      }
    });
  }

  public function down(): void
  {
    Schema::table('event_registrations', function (Blueprint $table) {
      if (Schema::hasColumn('event_registrations', 'check_in_method')) $table->dropColumn('check_in_method');
      if (Schema::hasColumn('event_registrations', 'checked_in_at')) $table->dropColumn('checked_in_at');
    });
  }
};
