<?php

namespace App\Repositories;

use App\Models\Kelas;
use App\Repositories\Contracts\KelasRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class KelasRepository
 *
 * Repository for managing class data operations.
 *
 * @package App\Repositories
 */
class KelasRepository extends BaseRepository implements KelasRepositoryInterface
{
    /**
     * KelasRepository constructor.
     *
     * @param Kelas $model
     */
    public function __construct(Kelas $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getByTingkat(string $tingkat): Collection
    {
        return $this->model
            ->tingkat($tingkat)
            ->with('waliKelas')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getByJurusan(string $jurusan): Collection
    {
        return $this->model
            ->jurusan($jurusan)
            ->with('waliKelas')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getAllWithWaliKelas(): Collection
    {
        return $this->model
            ->with('waliKelas')
            ->get();
    }
}
