<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class MainRepository 
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function countAll()
    {
        return $this->model->count();
    }

    public function getWhere(array $where, ?array $relations, ?int $limit)
    {
        return $this->model->where($where)->when(
            $relations, 
            fn ($q) => $q->with($relations)
        )->when(
            $limit, 
            fn($q) => $q->limit($limit)
        )->get();
    }

    public function paginateWhere(int $perPage, ?callable $where, ?array $relations, ?array $orderBy = ['id', 'ASC'])
    {
        return $this->model->where($where)
            ->when($relations, fn ($q) => $q->with($relations))
            ->when($where, fn($q) => $q->where($where))
            ->orderBy($orderBy[0], $orderBy[1])
            ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data) 
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id) 
    {
        return $this->findById($id)->update($data);
    }

    public function delete($id) 
    {
        return $this->findById($id)->delete();
    }
}