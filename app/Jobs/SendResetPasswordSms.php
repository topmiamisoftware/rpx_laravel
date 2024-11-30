<?php

namespace App\Jobs;

use App\Models\SystemSms;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendResetPasswordSms implements ShouldQueue
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
        private string $token
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

        app(UserService::class)->sendPasswordResetSms(
            $spotbieUser->phone_number,
            $this->user->id,
            $spotbieUser,
            $this->systemSms,
            $this->token,
            $this->user->email
        );
    }
}
