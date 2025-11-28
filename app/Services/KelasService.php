<?php

namespace App\Services;

use App\Repositories\Contracts\KelasRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class KelasService
 *
 * Service class for handling class-related business logic.
 * Manages class operations and student assignments.
 *
 * @package App\Services
 */
class KelasService
{
    /**
     * @var KelasRepositoryInterface
     */
    protected KelasRepositoryInterface $kelasRepository;

    /**
     * KelasService constructor.
     *
     * @param KelasRepositoryInterface $kelasRepository Class repository instance
     */
    public function __construct(KelasRepositoryInterface $kelasRepository)
    {
        $this->kelasRepository = $kelasRepository;
    }

    /**
     * Get all classes with homeroom teachers.
     *
     * @return Collection Collection of classes with loaded relationships
     */
    public function getAllClasses(): Collection
    {
        return $this->kelasRepository->getAllWithWaliKelas();
    }

    /**
     * Get classes by grade level.
     *
     * @param string $tingkat Grade level (X, XI, XII)
     * @return Collection Collection of classes in the specified grade level
     */
    public function getClassesByGrade(string $tingkat): Collection
    {
        return $this->kelasRepository->getByTingkat($tingkat);
    }

    /**
     * Get classes by major.
     *
     * @param string $jurusan Major (RPL, TKJ, MM, etc.)
     * @return Collection Collection of classes in the specified major
     */
    public function getClassesByMajor(string $jurusan): Collection
    {
        return $this->kelasRepository->getByJurusan($jurusan);
    }

    /**
     * Find a class by ID.
     *
     * @param int $id Class ID
     * @return Model|null Class model or null if not found
     */
    public function findClass(int $id): ?Model
    {
        return $this->kelasRepository->find($id);
    }

    /**
     * Create a new class.
     *
     * @param array $data Class data
     * @return Model Created class model
     */
    public function createClass(array $data): Model
    {
        return $this->kelasRepository->create($data);
    }

    /**
     * Update class information.
     *
     * @param int $id Class ID
     * @param array $data Updated class data
     * @return bool True if update successful, false otherwise
     */
    public function updateClass(int $id, array $data): bool
    {
        return $this->kelasRepository->update($id, $data);
    }

    /**
     * Delete a class.
     *
     * @param int $id Class ID
     * @return bool True if deletion successful, false otherwise
     */
    public function deleteClass(int $id): bool
    {
        return $this->kelasRepository->delete($id);
    }

    /**
     * Assign homeroom teacher to a class.
     *
     * @param int $kelasId Class ID
     * @param int $guruId Teacher/User ID
     * @return bool True if assignment successful, false otherwise
     */
    public function assignHomeroomTeacher(int $kelasId, int $guruId): bool
    {
        return $this->kelasRepository->update($kelasId, [
            'wali_kelas_id' => $guruId,
        ]);
    }
}
