<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // name, points, category, weight (to match the sheet order)
        $rows = [
            // Left column
            ['Millionaire Suit',                3, 'Discipline',  1],
            ['Calls / Email / SocMed',          1, 'Sales',       2],
            ['Appointments Secured',            2, 'Sales',       3],
            ['Referrals (min 7)',               1, 'Sales',       4],
            ['Sales Presentations',             3, 'Sales',       5],
            ['FTF / Booth / Nesting',           2, 'Sales',       6],
            ['Cases Closed',                    4, 'Sales',       7],
            ['Servicing / Follow-up',           2, 'Sales',       8],
            ['Joint Field Work',                2, 'Sales',       9],
            ['Be Early to Training',            2, 'Training',   10],

            // Right column
            ['Tahajjud / Hajat / Dhuha',        3, 'Mindset',    11],
            ['On The Road 7:30 AM',             2, 'Discipline', 12],
            ['Affirmation',                      3, 'Mindset',    13],
            ['Write my Goal Book',               3, 'Mindset',    14],
            ['Read Book',                        2, 'Training',   15],
            ['Update Upline',                    2, 'Team',       16],
            ['Sharing in Training',              5, 'Training',   17],
            ['Personal Coaching',                3, 'Training',   18],
            ['Career Presentation/BOP',          3, 'Recruitment',19],
            ['Sign Up New Downlines',            4, 'Recruitment',20],
        ];

        $insert = [];
        foreach ($rows as [$name, $points, $category, $weight]) {
            $insert[] = [
                'name'         => $name,
                'slug'         => Str::slug($name, '_'),
                'category'     => $category,
                'point_value'  => $points,
                'weight'       => $weight,
                'is_active'    => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        DB::table('activity_types')->upsert($insert, ['slug'], ['point_value','category','weight','is_active','updated_at']);
    }
}
