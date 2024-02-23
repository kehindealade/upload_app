<?php

namespace App\Http\Interfaces;

interface LoginRepositoryInterface
{
    /**
     * Handle the user login.
     *
     * @param  \Illuminate\Http\Request  $request The request object containing login credentials.
     * @return mixed
     */
    public function login($request);

    /**
     * Handle the user logout.
     *
     * @param  \Illuminate\Http\Request  $request The request object containing logout details.
     * @return mixed
     */
    public function logout($request);
}
