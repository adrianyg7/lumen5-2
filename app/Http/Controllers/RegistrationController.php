<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UUID;
use App\Services\Mailer;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\UserProvider;

class RegistrationController extends Controller
{
    /**
     * The application mailer.
     *
     * @var App\Services\Mailer
     */
    protected $mail;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $users;

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Services\Mailer  $mail
     * @param  \Illuminate\Contracts\Auth\UserProvider  $users
     * @return void
     */
    public function __construct(Mailer $mail, UserProvider $users)
    {
        $this->mail = $mail;
        $this->users = $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|string|min:4',
        ]);

        $user = User::create($request->input());

        UUID::generate($user, ['api_token', 'verification_code']);

        $this->mail->userVerification($user);

        return success(trans('registration.store', ['email' => $user->email]));
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
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|string',
        ]);

        $user = User::where('verification_code', $request->token)
                    ->whereNull('verified_at')
                    ->first();

        if (! $user) {
            return error(trans('registration.invalid_token'), [], 422);
        }

        $user->verification_code = null;
        $user->verified_at = now();
        $user->save();

        return success(trans('registration.update'));
    }

    /**
     * Resends the verification email to user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        $user = $this->users->retrieveByCredentials($request->only('email'));

        if ($user and ! $user->isVerified()) {
            UUID::generate($user, 'verification_code');

            $this->mail->userVerification($user);
        }

        return success(trans('registration.store', ['email' => $user->email]));
    }
}
