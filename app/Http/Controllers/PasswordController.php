<?php

namespace App\Http\Controllers;

use Password;
use Illuminate\Http\Request;
use App\Services\PasswordBroker;

class PasswordController extends Controller
{
    /**
     * The password broker instance.
     *
     * @var \App\Services\PasswordBroker
     */
    protected $broker;

    /**
     * Create a new password broker instance.
     *
     * @param  \App\Services\PasswordBroker  $broker
     * @return void
     */
    public function __construct(PasswordBroker $broker)
    {
        $this->broker = $broker;
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $subject = trans('passwords.subject', [
            'project' => config('project.name'),
        ]);

        Password::broker()->sendResetLink($request->only('email'), function ($message) use ($subject) {
            $message->subject($subject);
        });

        return success(trans('passwords.sent', ['email' => $request->email]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $token
     * @return Response
     */
    public function edit($token)
    {
        
    }

    /**
     * Reset the given user's password.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'token'    => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $response = $this->broker->reset($request->only('token', 'password'), function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return success(trans($response));

            default:
                return error(trans($response), [], 422);
        }
    }
}
