<?php

namespace App\Repositories;

use App\Masakan;

class MasakanRepository extends MainRepository
{
    public function __construct(Masakan $model)
    {
        $this->model = $model;
    }
}