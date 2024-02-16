<?php

namespace App\Repositories;

use App\Kategori;

class KategoriRepository extends MainRepository
{
    public function __construct(Kategori $model)
    {
        $this->model = $model;
    }
}