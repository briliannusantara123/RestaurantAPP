<?php

namespace App\Repositories;

use App\User;

class UserRepository extends MainRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }
}