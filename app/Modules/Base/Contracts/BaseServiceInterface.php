<?php

namespace App\Modules\Base\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseServiceInterface
{
    /**
     * Mengambil semua entitas bisnis.
     */
    public function getAll(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Mengambil entitas bisnis terpaginasi.
     */
    public function getPaginated(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    /**
     * Mengambil entitas bisnis berdasarkan ID.
     */
    public function getById(int|string $id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Menyimpan data baru dengan penanganan keamanan transaksi database.
     */
    public function store(array $data): Model;

    /**
     * Memperbarui data yang ada dengan penanganan keamanan transaksi database.
     */
    public function update(int|string $id, array $data): bool;

    /**
     * Menghapus entitas bisnis (soft-delete).
     */
    public function remove(int|string $id): bool;

    /**
     * Mengembalikan entitas bisnis yang telah dihapus.
     */
    public function restore(int|string $id): bool;
}
