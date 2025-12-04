<?php

namespace Database\Factories;

use App\Models\Conference;
use App\Models\Correspondence;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CorrespondenceFactory extends Factory
{
    protected $model = Correspondence::class;

    public function definition(): array
    {
        $categories = [
            'invitation', 'member_consultation', 'research', 'attendance',
            'logistics', 'finance', 'royal_court', 'diplomatic', 'security',
            'press', 'membership', 'thanks', 'general',
        ];

        $workflowGroups = [
            'pre_conference', 'scientific', 'logistics', 'media',
            'finance', 'membership', 'royal', 'security', 'general_ops',
        ];

        $statuses = [
            'draft', 'sent', 'delivered', 'received', 'replied',
            'approved', 'rejected', 'pending', 'archived',
        ];

        $category = fake()->randomElement($categories);
        $direction = fake()->randomElement(['outgoing', 'incoming']);
        $requiresFollowUp = fake()->boolean(70);

        return [
            'conference_id' => Conference::factory(),
            'member_id' => fake()->boolean(60) ? Member::factory() : null,
            'created_by' => User::factory(),
            'direction' => $direction,
            'category' => $category,
            'workflow_group' => fake()->randomElement($workflowGroups),
            'ref_number' => $this->generateRefNumber($category),
            'correspondence_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'recipient_entity' => $direction === 'outgoing'
                ? fake()->name() . ', ' . fake()->jobTitle()
                : null,
            'sender_entity' => $direction === 'incoming'
                ? fake()->name() . ', ' . fake()->jobTitle()
                : 'Conference Organizing Committee',
            'subject' => $this->generateSubject($category),
            'content' => fake()->paragraphs(3, true),
            'header' => fake()->boolean(40) ? [
                ['label' => 'To', 'value' => fake()->name()],
                ['label' => 'From', 'value' => 'Conference Committee'],
                ['label' => 'CC', 'value' => fake()->email()],
            ] : null,
            'file_path' => fake()->boolean(30) ? 'correspondences/' . fake()->uuid() . '.pdf' : null,
            'response_received' => fake()->boolean(40),
            'response_date' => fake()->boolean(40) ? fake()->dateTimeBetween('-3 months', 'now') : null,
            'status' => fake()->randomElement($statuses),
            'priority' => fake()->numberBetween(1, 5),
            'requires_follow_up' => $requiresFollowUp,
            'follow_up_at' => $requiresFollowUp ? fake()->dateTimeBetween('now', '+2 months') : null,
            'last_of_type' => false,
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }

    private function generateRefNumber(string $category): string
    {
        $prefix = match ($category) {
            'invitation' => 'INV',
            'member_consultation' => 'MC',
            'research' => 'RES',
            'attendance' => 'ATT',
            'logistics' => 'LOG',
            'finance' => 'FIN',
            'royal_court' => 'RC',
            'diplomatic' => 'DIP',
            'security' => 'SEC',
            'press' => 'PRS',
            'membership' => 'MBR',
            'thanks' => 'THX',
            default => 'GEN',
        };

        $year = fake()->numberBetween(2024, 2025);
        $number = fake()->unique()->numberBetween(1, 9999);

        return sprintf('%s-%d-%04d', $prefix, $year, $number);
    }

    private function generateSubject(string $category): string
    {
        $subjects = [
            'invitation' => [
                'Invitation to Participate in Conference',
                'Invitation to Speak at Conference Session',
                'Invitation to Join Scientific Committee',
                'Invitation to Keynote Address',
            ],
            'member_consultation' => [
                'Consultation Request for Research Topic',
                'Expert Opinion Request',
                'Consultation on Conference Theme',
            ],
            'research' => [
                'Research Paper Submission',
                'Call for Papers',
                'Research Collaboration Proposal',
            ],
            'logistics' => [
                'Travel Arrangement Confirmation',
                'Hotel Booking Confirmation',
                'Venue Setup Requirements',
            ],
            'finance' => [
                'Budget Approval Request',
                'Payment Authorization',
                'Financial Report Submission',
            ],
            'royal_court' => [
                'Royal Patronage Request',
                'Protocol Arrangements',
                'Official Invitation to Royal Guest',
            ],
            'diplomatic' => [
                'Diplomatic Mission Coordination',
                'Embassy Notification',
                'International Delegation Arrangements',
            ],
            'security' => [
                'Security Clearance Request',
                'VIP Protection Arrangements',
                'Security Protocol Update',
            ],
            'press' => [
                'Press Conference Announcement',
                'Media Coverage Request',
                'Press Release Distribution',
            ],
            'membership' => [
                'Membership Application',
                'Membership Renewal Notice',
                'Membership Benefits Update',
            ],
            'thanks' => [
                'Thank You for Your Participation',
                'Appreciation Letter',
                'Acknowledgment of Contribution',
            ],
            'general' => [
                'General Inquiry',
                'Information Request',
                'Administrative Notice',
            ],
        ];

        return fake()->randomElement($subjects[$category] ?? $subjects['general']);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'response_received' => false,
            'response_date' => null,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }

    public function replied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'replied',
            'response_received' => true,
            'response_date' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 1,
            'requires_follow_up' => true,
            'follow_up_at' => fake()->dateTimeBetween('now', '+1 week'),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'requires_follow_up' => true,
            'follow_up_at' => fake()->dateTimeBetween('-2 weeks', '-1 day'),
            'status' => fake()->randomElement(['sent', 'delivered', 'pending']),
        ]);
    }
}
