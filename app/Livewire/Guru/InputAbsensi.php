<?php

namespace App\Livewire\Guru;

use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Class InputAbsensi
 *
 * Livewire component for recording student attendance.
 * Supports both individual and bulk attendance input.
 *
 * @package App\Livewire\Guru
 */
class InputAbsensi extends Component
{
    /**
     * @var int Selected schedule ID
     */
    public $jadwal_id;

    /**
     * @var string Attendance date
     */
    public $tanggal;

    /**
     * @var array List of students with attendance status
     */
    public $siswaList = [];

    /**
     * @var array Available schedules
     */
    public $jadwalOptions = [];

    /**
     * @var bool Loading state
     */
    public $isLoading = false;

    /**
     * Validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'tanggal' => 'required|date',
            'siswaList.*.status' => 'required|in:hadir,izin,sakit,alpha',
            'siswaList.*.keterangan' => 'nullable|string|max:500',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    protected function messages(): array
    {
        return [
            'jadwal_id.required' => 'Jadwal pelajaran harus dipilih',
            'tanggal.required' => 'Tanggal harus diisi',
            'siswaList.*.status.required' => 'Status kehadiran harus dipilih',
        ];
    }

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->tanggal = Carbon::now()->format('Y-m-d');
        $this->loadJadwalOptions();
    }

    /**
     * Load available teaching schedules for logged-in teacher.
     *
     * @return void
     */
    protected function loadJadwalOptions(): void
    {
        $this->jadwalOptions = JadwalPelajaran::query()
            ->whereHas('mataPelajaran', function ($query) {
                $query->where('guru_id', Auth::id());
            })
            ->with(['kelas', 'mataPelajaran'])
            ->get()
            ->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'label' => "{$jadwal->kelas->nama_kelas} - {$jadwal->mataPelajaran->nama_mapel} ({$jadwal->hari}, {$jadwal->jam_mulai->format('H:i')})",
                ];
            })
            ->toArray();
    }

    /**
     * Handle schedule selection change.
     * Load students for the selected class.
     *
     * @return void
     */
    public function updatedJadwalId(): void
    {
        if (!$this->jadwal_id) {
            $this->siswaList = [];
            return;
        }

        $this->isLoading = true;

        $jadwal = JadwalPelajaran::with('kelas')->find($this->jadwal_id);

        if (!$jadwal) {
            $this->siswaList = [];
            $this->isLoading = false;
            return;
        }

        $siswa = Siswa::where('kelas_id', $jadwal->kelas_id)
            ->with('user')
            ->orderBy('nis')
            ->get();

        // Check existing attendance
        $existingAbsensi = Absensi::where('jadwal_pelajaran_id', $this->jadwal_id)
            ->whereDate('tanggal', $this->tanggal)
            ->get()
            ->keyBy('siswa_id');

        $this->siswaList = $siswa->map(function ($s) use ($existingAbsensi) {
            $existing = $existingAbsensi->get($s->id);

            return [
                'id' => $s->id,
                'nis' => $s->nis,
                'nama' => $s->user->name,
                'status' => $existing ? $existing->status : 'alpha',
                'keterangan' => $existing ? $existing->keterangan : '',
                'absensi_id' => $existing ? $existing->id : null,
            ];
        })->toArray();

        $this->isLoading = false;
    }

    /**
     * Set all students' status to a specific value.
     *
     * @param string $status Attendance status
     * @return void
     */
    public function setAllStatus(string $status): void
    {
        foreach ($this->siswaList as $key => $siswa) {
            $this->siswaList[$key]['status'] = $status;
        }
    }

    /**
     * Save attendance records.
     *
     * @return void
     */
    public function simpan(): void
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $successCount = 0;
            $updateCount = 0;

            foreach ($this->siswaList as $siswa) {
                $data = [
                    'siswa_id' => $siswa['id'],
                    'jadwal_pelajaran_id' => $this->jadwal_id,
                    'tanggal' => $this->tanggal,
                    'status' => $siswa['status'],
                    'keterangan' => $siswa['keterangan'],
                    'waktu_absen' => $siswa['status'] === 'hadir' ? Carbon::now()->format('H:i:s') : null,
                    'dicatat_oleh' => Auth::id(),
                ];

                if ($siswa['absensi_id']) {
                    // Update existing
                    Absensi::where('id', $siswa['absensi_id'])->update($data);
                    $updateCount++;
                } else {
                    // Create new
                    Absensi::create($data);
                    $successCount++;
                }
            }

            DB::commit();

            $message = $successCount > 0
                ? "Berhasil menyimpan {$successCount} data absensi baru"
                : "Berhasil mengupdate {$updateCount} data absensi";

            session()->flash('success', $message);

            // Reload data to reflect updates
            $this->updatedJadwalId();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.guru.input-absensi')
            ->layout('layouts.guru');
    }
}
