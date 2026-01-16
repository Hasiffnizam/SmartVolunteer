<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->date('event_date');

            // morning / evening / night
            $table->enum('time_slot', ['morning', 'evening', 'night']);

            $table->string('location');

            // stored in storage/app/public/event-posters/...
            $table->string('poster_path')->nullable();

            // optional (future): draft/published/archived
            $table->string('status')->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
