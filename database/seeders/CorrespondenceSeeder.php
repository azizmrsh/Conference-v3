<?php

namespace Database\Seeders;

use App\Models\Correspondence;
use App\Models\Conference;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class CorrespondenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have required data
        $conferences = Conference::all();
        $members = Member::all();
        $users = User::all();

        if ($conferences->isEmpty() || $members->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Please seed Conferences, Members, and Users first!');
            return;
        }

        $this->command->info('Creating correspondences...');

        // Create various types of correspondences
        $categories = [
            'invitation', 'member_consultation', 'research', 'attendance',
            'logistics', 'finance', 'royal_court', 'diplomatic',
            'security', 'press', 'membership', 'thanks', 'general'
        ];

        foreach ($categories as $category) {
            // Create 3-5 correspondences per category
            $count = rand(3, 5);

            for ($i = 0; $i < $count; $i++) {
                $correspondence = Correspondence::factory()
                    ->for($conferences->random())
                    ->for($members->random())
                    ->for($users->random(), 'creator')
                    ->create([
                        'category' => $category,
                    ]);

                // Randomly apply states
                if (rand(0, 4) === 0) {
                    $correspondence->update(['status' => 'draft']);
                } elseif (rand(0, 3) === 0) {
                    $correspondence->update([
                        'status' => 'sent',
                        'requires_follow_up' => true,
                        'follow_up_at' => now()->addDays(rand(1, 14)),
                    ]);
                } elseif (rand(0, 2) === 0) {
                    $correspondence->update([
                        'status' => 'replied',
                        'response_received' => true,
                        'response_date' => now()->subDays(rand(1, 7)),
                    ]);
                }
            }
        }

        // Create some overdue follow-ups for testing
        Correspondence::factory()
            ->count(5)
            ->for($conferences->random())
            ->for($members->random())
            ->for($users->random(), 'creator')
            ->create([
                'status' => 'sent',
                'requires_follow_up' => true,
                'follow_up_at' => now()->subDays(rand(1, 10)),
            ]);

        $this->command->info('Created ' . Correspondence::count() . ' correspondences successfully!');
    }
}
