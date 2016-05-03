<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\UserProvider;

class SessionsController extends Controller
{
    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $users;

    /**
     * Create a new sessions instance.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $users
     * @return void
     */
    public function __construct(UserProvider $users)
    {
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
            'email'        => 'required|email',
            'password'     => 'required|string',
            'service'      => 'required|in:' . implode(',', Device::$services),
            'device_uuid'  => 'required|string',
            'device_token' => 'required|string',
        ]);

        $user = $this->users->retrieveByCredentials($request->only('email'));

        if (! $user or ! $this->users->validateCredentials($user, $request->only('password'))) {
            return error(trans('auth.failed'), [], 401);
        }

        if (! $user->isVerified()) {
            return error(trans('auth.not_verified'), [], 428);
        }

        Device::updateOrCreate([
            'device_uuid' => $request->device_uuid,
        ],[
            'user_id' => $user->id,
            'service' => $request->service,
            'device_token' => $request->device_token,
        ]);

        return success(trans('auth.logged_in'), ['api_token' => $user->api_token]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'api_token'   => 'required|string',
            'device_uuid' => 'required|string',
        ]);

        Device::where('device_uuid', $request->device_uuid)
            ->whereHas('user', function ($query) use ($request) {
                $query->where('api_token', $request->api_token);
            })->delete();

        return success(trans('auth.logged_out'));
    }
}
