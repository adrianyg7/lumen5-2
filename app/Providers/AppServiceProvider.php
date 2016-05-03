<?php

namespace App\Providers;

use App\Http\Response;
use App\Facades\ResponseFacade;
use Illuminate\Database\Connection;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\ConnectionInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserProvider::class, function ($app) {
            $model = config('auth.providers.users.model');

            return new EloquentUserProvider($app['hash'], $model);
        });

        $this->app->alias('mailer', Mailer::class);

        $this->app->configure('auth');
        $this->app->configure('mail');
        $this->app->configure('project');
    }
}
