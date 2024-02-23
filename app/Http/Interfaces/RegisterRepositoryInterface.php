<?php

namespace App\Http\Interfaces;

interface RegisterRepositoryInterface
{
    /**
     * Handle the user registration.
     *
     * @param  \Illuminate\Http\Request  $request The request object containing registration data.
     * @return mixed
     */
    public function register($request);
}
