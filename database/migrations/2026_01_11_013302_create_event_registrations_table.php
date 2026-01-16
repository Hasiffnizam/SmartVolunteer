<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('event_registrations', function (Blueprint $table) {
      $table->id();

      $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
      $table->foreignId('role_task_id')->constrained('role_tasks')->cascadeOnDelete();

      // volunteer is a user
      $table->foreignId('volunteer_id')->constrained('users')->cascadeOnDelete();

      $table->timestamp('joined_at')->useCurrent();

      // 1 volunteer can join an event only once (1 slot)
      $table->unique(['event_id', 'volunteer_id']);

      // optional: prevents duplicate slot claim for same role_task too
      $table->unique(['role_task_id', 'volunteer_id']);

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_registrations');
  }
};
