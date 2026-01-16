<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            $table->string('name'); // e.g. "First Aid", "Cooking", "Photography"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_skills');
    }
};
