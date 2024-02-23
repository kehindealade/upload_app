<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * The User model instance.
     *
     * @var \App\Models\User
     */
    protected $model;

    /**
     * Create a new instance of the repository.
     *
     * @param  \App\Models\User  $model The User model instance.
     * @return void
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Find a user by their ID.
     *
     * @param  int  $id The ID of the user to find.
     * @return mixed
     */
    public function findById($id)
    {
    }

    /**
     * Find a user by their email address.
     *
     * @param  string  $email The email address of the user to find.
     * @return \App\Models\User
     */
    public function findByEmail($email)
    {
        return $this->model->whereEmail($email)->firstOrFail();
    }

    /**
     * Create a new user with the given data.
     *
     * @param  array  $data The data to create the user with.
     * @return \App\Models\User
     */
    public function create(array $data)
    {
        return $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'photo_url' => $data['photo_name'],
        ]);
    }

    /**
     * Authenticate a user with the provided login details.
     *
     * @param  array  $loginDetails The login details provided by the user.
     * @return void
     */
    public function authenticate(array $loginDetails)
    {
    }

    public function current_user()
    {
        return auth()->user();
    }
}
