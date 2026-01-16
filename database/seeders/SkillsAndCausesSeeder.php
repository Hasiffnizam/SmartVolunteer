<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\Cause;
use Illuminate\Support\Str;

class SkillsAndCausesSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Communication',
            'Teamwork',
            'Leadership',
            'First Aid',
            'Cooking & Food Prep',
            'Logistics & Setup',
            'Crowd Control & Safety',
            'Registration & Admin Support',
            'Photography / Media',
            'Teaching / Facilitation',
        ];

        foreach ($skills as $name) {
            Skill::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        $causes = [
            'Environment',
            'Education',
            'Health',
            'Poverty',
            'Animal Welfare',
            'Community',
            'Disaster Relief',
            'Elderly Care',
            'Youth Development',
            'Other',
        ];

        foreach ($causes as $name) {
            Cause::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
