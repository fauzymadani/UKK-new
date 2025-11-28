<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface KelasRepositoryInterface
 *
 * Repository interface for class-related database operations.
 *
 * @package App\Repositories\Contracts
 */
interface KelasRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get classes by grade level.
     *
     * @param string $tingkat Grade level (X, XI, XII)
     * @return Collection
     */
    public function getByTingkat(string $tingkat): Collection;

    /**
     * Get classes by major.
     *
     * @param string $jurusan Major (RPL, TKJ, MM, etc.)
     * @return Collection
     */
    public function getByJurusan(string $jurusan): Collection;

    /**
     * Get classes with their homeroom teacher.
     *
     * @return Collection
     */
    public function getAllWithWaliKelas(): Collection;
}
