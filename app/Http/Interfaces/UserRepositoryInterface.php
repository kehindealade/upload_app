<?php

namespace App\Http\Interfaces;

interface UserRepositoryInterface
{
    /**
     * Find a user by their ID.
     *
     * @param  int  $id The ID of the user to find.
     * @return mixed
     */
    public function findById($id);

    /**
     * Find a user by their email address.
     *
     * @param  string  $email The email address of the user to find.
     * @return mixed
     */
    public function findByEmail($email);

    /**
     * Create a new user with the given data.
     *
     * @param  array  $data The data to create the user with.
     * @return mixed
     */
    public function create(array $data);

    /**
     * Get current authenticated User
     * @return \App\Models\User
     */
    public function current_user();
}
