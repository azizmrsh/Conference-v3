<?php

namespace App\Console\Commands;

use App\Models\Correspondence;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCorrespondenceReminders extends Command
{
    protected $signature = 'correspondence:send-reminders';

    protected $description = 'Send reminders for correspondences requiring follow-up';

    public function handle(): int
    {
        $this->info('Checking for correspondences requiring follow-up...');

        $pendingCorrespondences = Correspondence::pendingFollowUp()->get();

        if ($pendingCorrespondences->isEmpty()) {
            $this->info('No pending follow-ups found.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($pendingCorrespondences as $correspondence) {
            // Send notification to creator
            if ($correspondence->creator) {
                Notification::make()
                    ->title('Follow-up Required')
                    ->body("Correspondence '{$correspondence->subject}' (Ref: {$correspondence->ref_number}) requires follow-up.")
                    ->warning()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->button()
                            ->url(route('filament.admin.resources.correspondences.view', $correspondence)),
                    ])
                    ->sendToDatabase($correspondence->creator);

                // Optional: Send email notification
                if ($correspondence->creator->email) {
                    Mail::raw(
                        "Follow-up reminder for correspondence:\n\n" .
                        "Subject: {$correspondence->subject}\n" .
                        "Ref: {$correspondence->ref_number}\n" .
                        "Category: {$correspondence->category}\n" .
                        "Follow-up due: {$correspondence->follow_up_at->format('Y-m-d H:i')}\n\n" .
                        "Please review and take appropriate action.",
                        function ($message) use ($correspondence) {
                            $message->to($correspondence->creator->email)
                                ->subject("Follow-up Reminder: {$correspondence->ref_number}");
                        }
                    );
                }

                $count++;
            }
        }

        $this->info("Sent {$count} follow-up reminders.");

        return Command::SUCCESS;
    }
}
