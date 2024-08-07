<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SocialRecordSeeder extends Seeder
{
    public function run(): void
    {
        $socialRecord = [
            [
                'id' => 1,
                'social_accounts_id' => 1,
                'jumlah_followers' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'social_accounts_id' => 2,
                'jumlah_followers' => 2000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'social_accounts_id' => 3,
                'jumlah_followers' => 3000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('social_records')->insert($socialRecord);
    }
}
