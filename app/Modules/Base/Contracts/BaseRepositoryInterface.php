<?php

namespace App\Modules\Base\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Mendapatkan model yang diikat ke repository.
     */
    public function getModel(): Model;

    /**
     * Mendapatkan semua record.
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Mendapatkan record terpaginasi.
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    /**
     * Menemukan record berdasarkan ID.
     */
    public function find(int|string $id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Menemukan record berdasarkan ID atau lempar exception jika tidak ditemukan.
     */
    public function findOrFail(int|string $id, array $columns = ['*'], array $relations = []): Model;

    /**
     * Mencari record berdasarkan kolom tertentu.
     */
    public function findBy(string $column, mixed $value, array $columns = ['*'], array $relations = []): Collection;

    /**
     * Membuat record baru.
     */
    public function create(array $details): Model;

    /**
     * Memperbarui record berdasarkan ID.
     */
    public function update(int|string $id, array $details): bool;

    /**
     * Menghapus record (soft-delete).
     */
    public function delete(int|string $id): bool;

    /**
     * Mengembalikan record yang telah dihapus soft-delete.
     */
    public function restore(int|string $id): bool;
}
