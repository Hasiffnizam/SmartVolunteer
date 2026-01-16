<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('volunteer'); // admin | volunteer

            $table->date('dob')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('occupation', 80)->nullable();
            $table->text('address')->nullable();

            $table->json('skills')->nullable();
            $table->json('causes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role','dob','phone','gender','occupation','address','skills','causes'
            ]);
        });
    }
};