<?php

namespace Database\Seeders;

use App\Models\SalesPage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Demo Owner',
                'email' => 'owner@salespage.local',
            ],
            [
                'name' => 'Demo Marketer',
                'email' => 'marketer@salespage.local',
            ],
            [
                'name' => 'Demo Viewer',
                'email' => 'viewer@salespage.local',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::query()->updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ]
            );

            SalesPage::factory()
                ->count(2)
                ->for($user)
                ->create();
        }
    }
}
