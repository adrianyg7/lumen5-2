<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Mail\Mailer as MailerContract;

class Mailer
{
    /**
     * The mailer instance.
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mail;

    /**
     * Create a new application mailer instance.
     *
     * @param  \Illuminate\Contracts\Mail\Mailer  $mailer
     * @return void
     */
    public function __construct(MailerContract $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Sends a verification email to given user.
     *
     * @param  User  $user
     * @return void
     */
    public function userVerification(User $user)
    {
        $this->mail->send('emails.auth.verification', compact('user'), function ($message) use ($user) {
            $message->to($user->email);

            $message->subject(
                trans('registration.verification.subject', [
                    'project' => config('project.name'),
                ])
            );
        });
    }
}
