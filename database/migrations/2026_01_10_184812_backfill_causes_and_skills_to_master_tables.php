<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1) BACKFILL events.cause (string) -> events.cause_id (FK)
        |--------------------------------------------------------------------------
        */
        if (Schema::hasColumn('events', 'cause') && Schema::hasColumn('events', 'cause_id')) {

            $events = DB::table('events')
                ->select('id', 'cause')
                ->whereNotNull('cause')
                ->get();

            foreach ($events as $event) {
                $causeName = trim($event->cause);
                if ($causeName === '') continue;

                $cause = DB::table('causes')->where('name', $causeName)->first();

                if (!$cause) {
                    $causeId = DB::table('causes')->insertGetId([
                        'name' => $causeName,
                        'slug' => Str::slug($causeName),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $causeId = $cause->id;
                }

                DB::table('events')
                    ->where('id', $event->id)
                    ->update(['cause_id' => $causeId]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2) BACKFILL event_skills (string) -> event_skill (pivot)
        |--------------------------------------------------------------------------
        */
        if (
            Schema::hasTable('event_skills') &&
            Schema::hasTable('skills') &&
            Schema::hasTable('event_skill')
        ) {
            $rows = DB::table('event_skills')->select('event_id', 'name')->get();

            foreach ($rows as $row) {
                $skillName = trim((string) $row->name);
                if ($skillName === '') continue;

                $skill = DB::table('skills')->where('name', $skillName)->first();

                if (!$skill) {
                    $skillId = DB::table('skills')->insertGetId([
                        'name' => $skillName,
                        'slug' => Str::slug($skillName),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $skillId = $skill->id;
                }

                DB::table('event_skill')->updateOrInsert([
                    'event_id' => $row->event_id,
                    'skill_id' => $skillId,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3) BACKFILL users.skills & users.causes (JSON/string) -> pivot tables
        |--------------------------------------------------------------------------
        */
        if (
            Schema::hasColumn('users', 'skills') &&
            Schema::hasColumn('users', 'causes')
        ) {
            $users = DB::table('users')->select('id', 'skills', 'causes')->get();

            foreach ($users as $user) {

                // ---- skills ----
                $skills = is_string($user->skills)
                    ? json_decode($user->skills, true)
                    : $user->skills;

                if (is_array($skills)) {
                    foreach ($skills as $skillRaw) {
                        $skillName = trim((string) $skillRaw);
                        if ($skillName === '') continue;

                        $skill = DB::table('skills')->where('name', $skillName)->first();

                        if (!$skill) {
                            $skillId = DB::table('skills')->insertGetId([
                                'name' => $skillName,
                                'slug' => Str::slug($skillName),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            $skillId = $skill->id;
                        }

                        DB::table('skill_user')->updateOrInsert([
                            'user_id' => $user->id,
                            'skill_id' => $skillId,
                        ]);
                    }
                }

                // ---- causes ----
                $causes = is_string($user->causes)
                    ? json_decode($user->causes, true)
                    : $user->causes;

                if (is_array($causes)) {
                    foreach ($causes as $causeRaw) {
                        $causeName = trim((string) $causeRaw);
                        if ($causeName === '') continue;

                        $cause = DB::table('causes')->where('name', $causeName)->first();

                        if (!$cause) {
                            $causeId = DB::table('causes')->insertGetId([
                                'name' => $causeName,
                                'slug' => Str::slug($causeName),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } else {
                            $causeId = $cause->id;
                        }

                        DB::table('cause_user')->updateOrInsert([
                            'user_id' => $user->id,
                            'cause_id' => $causeId,
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // ❗ No rollback for data migration (intentional)
        // We do NOT delete pivot data automatically.
    }
};
