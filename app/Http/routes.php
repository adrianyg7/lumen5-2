<?php

$app->get('/', function() {
    return redirect('api.json');
});

$app->post('login', 'SessionsController@store');
$app->post('logout', 'SessionsController@destroy');

$app->post('register', 'RegistrationController@store');
// $app->get('verify-account/{token}', 'RegistrationController@edit');
$app->post('verify-account', 'RegistrationController@update');
$app->post('resend-verification-mail', 'RegistrationController@resend'); // needs throttle

$app->post('password/email', 'PasswordController@store'); // needs throttle
// $app->get('password/reset/{token}', 'PasswordController@edit');
$app->post('password/reset', 'PasswordController@update');

$app->group(['middleware' => 'auth', 'namespace' => 'App\Http\Controllers'], function () use ($app) {

    $app->get('users', 'UsersController@index');
    $app->post('users', 'UsersController@store');
    $app->get('users/{id}', 'UsersController@show');
    $app->post('users/{id}', 'UsersController@update');
    $app->post('users/{id}/destroy', 'UsersController@destroy');

});
