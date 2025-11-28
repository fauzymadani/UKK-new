<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\User;
use App\Models\JadwalPelajaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class DemoSeeder
 *
 * Seeds the database with demo data for testing.
 * Creates users, classes, students, subjects, and schedules.
 *
 * @package Database\Seeders
 */
class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Guru
        $guru1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'guru@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        $guru2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Create Kelas
        $kelas1 = Kelas::create([
            'nama_kelas' => 'X RPL 1',
            'tingkat' => 'X',
            'jurusan' => 'RPL',
            'wali_kelas_id' => $guru1->id,
        ]);

        $kelas2 = Kelas::create([
            'nama_kelas' => 'XI RPL 1',
            'tingkat' => 'XI',
            'jurusan' => 'RPL',
            'wali_kelas_id' => $guru2->id,
        ]);

        // Create Mata Pelajaran
        $mapel1 = MataPelajaran::create([
            'kode_mapel' => 'RPL01',
            'nama_mapel' => 'Pemrograman Web',
            'guru_id' => $guru1->id,
        ]);

        $mapel2 = MataPelajaran::create([
            'kode_mapel' => 'RPL02',
            'nama_mapel' => 'Basis Data',
            'guru_id' => $guru2->id,
        ]);

        // Create Siswa
        for ($i = 1; $i <= 5; $i++) {
            $userSiswa = User::create([
                'name' => 'Siswa ' . $i,
                'email' => 'siswa' . $i . '@sekolah.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]);

            Siswa::create([
                'user_id' => $userSiswa->id,
                'nis' => '2024000' . $i,
                'nisn' => '112233445' . $i,
                'kelas_id' => $kelas1->id,
                'jenis_kelamin' => $i % 2 == 0 ? 'P' : 'L',
                'alamat' => 'Jalan Raya No. ' . $i,
                'no_telp' => '08123456789' . $i,
            ]);
        }

        // Create Jadwal Pelajaran
        JadwalPelajaran::create([
            'kelas_id' => $kelas1->id,
            'mata_pelajaran_id' => $mapel1->id,
            'hari' => 'Senin',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '09:00:00',
        ]);

        JadwalPelajaran::create([
            'kelas_id' => $kelas1->id,
            'mata_pelajaran_id' => $mapel2->id,
            'hari' => 'Selasa',
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '11:00:00',
        ]);

        JadwalPelajaran::create([
            'kelas_id' => $kelas2->id,
            'mata_pelajaran_id' => $mapel1->id,
            'hari' => 'Rabu',
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '09:00:00',
        ]);

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@sekolah.com / password');
        $this->command->info('Guru: guru@sekolah.com / password');
        $this->command->info('Siswa: siswa1@sekolah.com / password');
    }
}
