<?php

namespace App\Repositories;

use App\Order;

class OrderRepository extends MainRepository
{
    public function __construct(Order $model)
    {
        $this->model = $model;
    }
}