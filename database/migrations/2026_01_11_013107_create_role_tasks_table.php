<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('role_tasks', function (Blueprint $table) {
      $table->id();

      // Each role task belongs to an event
      $table->foreignId('event_id')
            ->constrained('events')
            ->cascadeOnDelete();

      // Role task info
      $table->string('title');        // e.g. Registration Desk, Crowd Control
      $table->text('description')->nullable();

      // Slot management
      $table->unsignedInteger('slots');        // total slots
      $table->unsignedInteger('slots_taken')->default(0); // used slots

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('role_tasks');
  }
};
