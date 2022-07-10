<?php

namespace App\Mail\User;

use App\Models\SpotbieUser;
use App\Models\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $spotbieUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, SpotbieUser $spotbieUser)
    {
        $this->user = $user;
        $this->spotbieUser = $spotbieUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        return $this->from('welcome@spotbie.com', 'SpotBie.com')
                    ->subject('Welcome to SpotBie!')
                    ->markdown('emails.account_created', [
                        'user' => $this->user,
                        'spotbieUser' => $this->spotbieUser,
                    ]);
    }
}
