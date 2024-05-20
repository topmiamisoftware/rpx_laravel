<?php

namespace App\Jobs;

use App\Models\SystemSms;
use App\Models\User;
use App\Services\Rewards;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPointsRedeemedSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private User $user,
        private SystemSms $systemSms,
        private string $phoneNumber,
        private string $businessName,
        private bool $withLoginInstructions = false
    ){
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $spotbieUser = $this->user->spotbieUser()->first();

        app(Points::class)->redeemedPoints(
            $this->phoneNumber,
            $this->user->id,
            $spotbieUser,
            $this->systemSms,
            $this->businessName
        );
    }
}
