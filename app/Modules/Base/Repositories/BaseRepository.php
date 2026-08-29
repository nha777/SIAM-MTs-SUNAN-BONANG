<?php

namespace App\Modules\Base\Repositories;

use App\Modules\Base\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * Model instance yang dikaitkan dengan repositori ini.
     */
    protected Model $model;

    /**
     * BaseRepository Constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Mendapatkan model yang diikat ke repository.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Mendapatkan semua record.
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->get($columns);
    }

    /**
     * Mendapatkan record terpaginasi.
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->paginate($perPage, $columns);
    }

    /**
     * Menemukan record berdasarkan ID.
     */
    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id, $columns);
    }

    /**
     * Menemukan record berdasarkan ID atau lempar exception jika tidak ditemukan.
     */
    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model
    {
        return $this->model->with($relations)->findOrFail($id, $columns);
    }

    /**
     * Mencari record berdasarkan kolom tertentu.
     */
    public function findBy(string $column, mixed $value, array $columns = ['*'], array $relations = []): Collection
    {
        return $this->model->with($relations)->where($column, $value)->get($columns);
    }

    /**
     * Membuat record baru.
     */
    public function create(array $details): Model
    {
        return $this->model->create($details);
    }

    /**
     * Memperbarui record berdasarkan ID.
     */
    public function update(int|string $id, array $details): bool
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }
        return $record->update($details);
    }

    /**
     * Menghapus record (soft-delete).
     */
    public function delete(int|string $id): bool
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }
        return $record->delete();
    }

    /**
     * Mengembalikan record yang telah dihapus soft-delete.
     */
    public function restore(int|string $id): bool
    {
        $record = $this->model->onlyTrashed()->find($id);
        if (!$record) {
            return false;
        }
        return $record->restore();
    }
}
