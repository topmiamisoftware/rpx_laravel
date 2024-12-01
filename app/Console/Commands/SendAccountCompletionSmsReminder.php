<?php

namespace App\Console\Commands;

use App\Jobs\SendAccountCompletionReminderSms;
use App\Jobs\SendAccountCreatedThroughBusinessSms;
use App\Models\Business;
use App\Models\SpotbieUser;
use App\Models\SystemSms;
use App\Models\User;
use Illuminate\Console\Command;

class SendAccountCompletionSmsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account_completion_sms:send {business_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder to users whose accounts need to be completed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $businessId = $this->argument('business_id');

        $userList = SpotbieUser::where('account_completed', '=', false)
            ->where('phone_number', '!=', null)
            ->where('created_in_business', $businessId)
            ->get();

        $userList->each(function ($spotbieUser) use ($businessId) {
            $user = User::find($spotbieUser->id);
            $sms = app(SystemSms::class)->createAccountCompletionReminderSms($user, $spotbieUser->phone_number);
            $business = Business::find($businessId);
            $businessName = $business->name;

            $portalUrl = '';
            if (env('APP_ENV') === 'staging') {
                $portalUrl = 'https://personal-demo.spotbie.com/community/'.$business->qr_code_link;
            } else if(env('APP_ENV') === 'production') {
                $portalUrl = 'https://home.spotbie.com/community/' . $business->qr_code_link;
            }

            SendAccountCompletionReminderSms::dispatch($user, $sms, $spotbieUser->phone_number, $businessName, $portalUrl)
                ->onQueue(config('spotbie.sms.queue'));
        });
    }
}
