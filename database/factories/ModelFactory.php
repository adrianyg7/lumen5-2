<?php

$factory->define(App\Models\User::class, function ($faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => '123456',
        'api_token' => str_random(60),
        'verified_at' => now(),
        'remember_token' => str_random(10),
    ];
});
