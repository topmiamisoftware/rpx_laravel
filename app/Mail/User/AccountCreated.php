<?php

namespace App\Mail\User;

use App\Models\SpotbieUser;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $spotbieUser;
    protected $withLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, SpotbieUser $spotbieUser, bool $withLink)
    {
        $this->user = $user;
        $this->spotbieUser = $spotbieUser;
        $this->withLink = $withLink;
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
                        'user'        => $this->user,
                        'spotbieUser' => $this->spotbieUser,
                        'withLink' => $this->withLink
                    ]);
    }
}
