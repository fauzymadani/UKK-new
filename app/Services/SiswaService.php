<?php

namespace App\Services;

use App\Models\Siswa;
use App\Repositories\Contracts\SiswaRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Throwable;

/**
 * Class SiswaService
 *
 * Service class for handling student-related business logic.
 * This layer sits between controllers and repositories, managing complex operations.
 *
 * @package App\Services
 */
class SiswaService
{
    /**
     * @var SiswaRepositoryInterface
     */
    protected SiswaRepositoryInterface $siswaRepository;

    /**
     * SiswaService constructor.
     *
     * @param SiswaRepositoryInterface $siswaRepository Student repository instance
     */
    public function __construct(SiswaRepositoryInterface $siswaRepository)
    {
        $this->siswaRepository = $siswaRepository;
    }

    /**
     * Get all students with their relationships.
     *
     * @return Collection Collection of students with loaded relationships
     */
    public function getAllStudents(): Collection
    {
        return $this->siswaRepository->getAllWithRelations();
    }

    /**
     * Get paginated list of students.
     *
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function getPaginatedStudents(int $perPage = 15): LengthAwarePaginator
    {
        return $this->siswaRepository->paginate($perPage);
    }

    /**
     * Get students by class ID.
     *
     * @param int $kelasId Class ID
     * @return Collection Collection of students in the specified class
     */
    public function getStudentsByClass(int $kelasId): Collection
    {
        return $this->siswaRepository->getByKelas($kelasId);
    }

    /**
     * Find a student by ID.
     *
     * @param int $id Student ID
     * @return Model|null Student model or null if not found
     */
    public function findStudent(int $id): ?Model
    {
        return $this->siswaRepository->find($id);
    }

    /**
     * Find a student by NIS (Student Identification Number).
     *
     * @param string $nis Student identification number
     * @return Model|null Student model or null if not found
     */
    public function findStudentByNis(string $nis): ?Model
    {
        return $this->siswaRepository->findByNis($nis);
    }

    /**
     * Create a new student with user account.
     *
     * This method handles the creation of both user and student records
     * in a single transaction for data integrity.
     *
     * @param array $data Student and user data
     * @return Model Created student model
     * @throws Exception|Throwable If creation fails
     */
    public function createStudent(array $data): Model
    {
        try {
            \DB::beginTransaction();

            // Create user account first
            $user = \App\Models\User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'siswa',
            ]);

            // Create student record
            $siswa = $this->siswaRepository->create([
                'user_id' => $user->id,
                'nis' => $data['nis'],
                'nisn' => $data['nisn'],
                'kelas_id' => $data['kelas_id'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'alamat' => $data['alamat'] ?? null,
                'no_telp' => $data['no_telp'] ?? null,
                'foto' => $data['foto'] ?? null,
            ]);

            \DB::commit();

            return $siswa->load(['user', 'kelas']);
        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update student information.
     *
     * @param int $id Student ID
     * @param array $data Updated student data
     * @return bool True if update successful, false otherwise
     * @throws Exception|Throwable If update fails
     */
    public function updateStudent(int $id, array $data): bool
    {
        try {
            \DB::beginTransaction();

            $siswa = $this->siswaRepository->findOrFail($id);

            // Update user data if provided
            if (isset($data['name']) || isset($data['email'])) {
                $userData = [];
                if (isset($data['name'])) $userData['name'] = $data['name'];
                if (isset($data['email'])) $userData['email'] = $data['email'];
                if (isset($data['password'])) $userData['password'] = Hash::make($data['password']);

                $siswa->user->update($userData);
            }

            // Update student data
            $result = $this->siswaRepository->update($id, [
                'nis' => $data['nis'] ?? $siswa->nis,
                'nisn' => $data['nisn'] ?? $siswa->nisn,
                'kelas_id' => $data['kelas_id'] ?? $siswa->kelas_id,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? $siswa->jenis_kelamin,
                'alamat' => $data['alamat'] ?? $siswa->alamat,
                'no_telp' => $data['no_telp'] ?? $siswa->no_telp,
                'foto' => $data['foto'] ?? $siswa->foto,
            ]);

            \DB::commit();

            return $result;
        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a student and their associated user account.
     *
     * @param int $id Student ID
     * @return bool True if deletion successful, false otherwise
     * @throws Exception|Throwable If deletion fails
     */
    public function deleteStudent(int $id): bool
    {
        try {
            \DB::beginTransaction();

            $siswa = $this->siswaRepository->findOrFail($id);
            $userId = $siswa->user_id;

            // Delete student record (will cascade due to foreign key)
            $result = $this->siswaRepository->delete($id);

            // Delete user account
            \App\Models\User::destroy($userId);

            \DB::commit();

            return $result;
        } catch (Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get student attendance percentage for a specific period.
     *
     * @param int $siswaId Student ID
     * @param string|null $startDate Start date (Y-m-d format)
     * @param string|null $endDate End date (Y-m-d format)
     * @return float Attendance percentage (0-100)
     */
    public function getAttendancePercentage(int $siswaId, ?string $startDate = null, ?string $endDate = null): float
    {
        return $this->siswaRepository->getAttendancePercentage($siswaId, $startDate, $endDate);
    }
}
