<?php

namespace App\Modules\Base\Services;

use App\Modules\Base\Contracts\BaseServiceInterface;
use App\Modules\Base\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class BaseService implements BaseServiceInterface
{
    /**
     * Repository Instance yang terkait dengan Service ini.
     */
    protected BaseRepositoryInterface $repository;

    /**
     * BaseService Constructor.
     */
    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Mengambil semua entitas bisnis.
     */
    public function getAll(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->repository->all($columns, $relations);
    }

    /**
     * Mengambil entitas bisnis terpaginasi.
     */
    public function getPaginated(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $columns, $relations);
    }

    /**
     * Mengambil entitas bisnis berdasarkan ID.
     */
    public function getById(int|string $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->repository->find($id, $columns, $relations);
    }

    /**
     * Menyimpan data baru dengan penanganan keamanan transaksi database.
     */
    public function store(array $data): Model
    {
        DB::beginTransaction();

        try {
            $model = $this->repository->create($data);
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat entitas bisnis: ' . $e->getMessage(), [
                'service' => static::class,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Memperbarui data yang ada dengan penanganan keamanan transaksi database.
     */
    public function update(int|string $id, array $data): bool
    {
        DB::beginTransaction();

        try {
            $status = $this->repository->update($id, $data);
            DB::commit();
            return $status;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui entitas bisnis: ' . $e->getMessage(), [
                'service' => static::class,
                'id' => $id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Menghapus entitas bisnis (soft-delete).
     */
    public function remove(int|string $id): bool
    {
        DB::beginTransaction();

        try {
            $status = $this->repository->delete($id);
            DB::commit();
            return $status;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus entitas bisnis: ' . $e->getMessage(), [
                'service' => static::class,
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Mengembalikan entitas bisnis yang telah dihapus.
     */
    public function restore(int|string $id): bool
    {
        DB::beginTransaction();

        try {
            $status = $this->repository->restore($id);
            DB::commit();
            return $status;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengembalikan entitas bisnis: ' . $e->getMessage(), [
                'service' => static::class,
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
