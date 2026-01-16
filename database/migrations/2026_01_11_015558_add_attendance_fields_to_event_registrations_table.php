<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('event_registrations', function (Blueprint $table) {
      if (!Schema::hasColumn('event_registrations', 'present')) {
        $table->boolean('present')->default(false)->after('joined_at');
      }
      if (!Schema::hasColumn('event_registrations', 'note')) {
        $table->string('note', 255)->nullable()->after('present');
      }
    });
  }

  public function down(): void
  {
    Schema::table('event_registrations', function (Blueprint $table) {
      if (Schema::hasColumn('event_registrations', 'note')) $table->dropColumn('note');
      if (Schema::hasColumn('event_registrations', 'present')) $table->dropColumn('present');
    });
  }
};
