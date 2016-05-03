<?php

namespace App\Services;

use DB;
use Closure;
use Password;
use Illuminate\Contracts\Auth\UserProvider;

class PasswordBroker
{
    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $users;

    /**
     * The number of seconds a token should last.
     *
     * @var int
     */
    protected $expires;

    /**
     * The token database table.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new password broker instance.
     *
     * @param  UserProvider  $users
     * @param  int  $expires
     * @return void
     */
    public function __construct(UserProvider $users, $expires, $table)
    {
        $this->users = $users;
        $this->expires = $expires * 60;
        $this->table = $table;
    }

    /**
     * Reset password for given credenials.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  array  $credencials
     * @param  \Closure  $callback
     * @return void
     */
    public function reset(array $credencials, Closure $callback)
    {
        if (! $token = $this->tokenExists($credencials['token'])) {
            return Password::INVALID_TOKEN;
        }

        $user = $this->users->retrieveByCredentials(array_only($credencials, 'email'));

        call_user_func($callback, $user, $credencials['password']);

        $this->deleteToken($credencials['token']);

        return Password::PASSWORD_RESET;
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  string  $token
     * @return mixed
     */
    protected function tokenExists($token)
    {
        $token = (array) $this->getTable()->where('token', $token)->first();

        if (! $token or $this->tokenExpired($token)) {
            return false;
        }

        return $token;
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return DB::table($this->table);
    }

    /**
     * Determine if the token has expired.
     *
     * @param  array  $token
     * @return bool
     */
    protected function tokenExpired($token)
    {
        $expirationTime = strtotime($token['created_at']) + $this->expires;

        return $expirationTime < $this->getCurrentTime();
    }

    /**
     * Get the current UNIX timestamp.
     *
     * @return int
     */
    protected function getCurrentTime()
    {
        return time();
    }

    /**
     * Delete a token record by token.
     *
     * @param  string  $token
     * @return void
     */
    public function deleteToken($token)
    {
        $this->getTable()->where('token', $token)->delete();
    }
}
