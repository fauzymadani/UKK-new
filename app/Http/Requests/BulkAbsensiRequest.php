<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class BulkAbsensiRequest
 *
 * Form request for validating bulk attendance recording data.
 * Handles validation rules for recording multiple attendance records at once.
 *
 * @package App\Http\Requests
 */
class BulkAbsensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if user is authorized, false otherwise
     */
    public function authorize(): bool
    {
        // Admin and guru can record bulk attendance
        return $this->user() && in_array($this->user()->role, ['admin', 'guru']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string> Validation rules
     */
    public function rules(): array
    {
        return [
            'attendance' => ['required', 'array', 'min:1'],
            'attendance.*.siswa_id' => ['required', 'integer', 'exists:siswa,id'],
            'attendance.*.jadwal_pelajaran_id' => ['required', 'integer', 'exists:jadwal_pelajaran,id'],
            'attendance.*.tanggal' => ['required', 'date', 'date_format:Y-m-d'],
            'attendance.*.status' => ['required', 'in:hadir,izin,sakit,alpha'],
            'attendance.*.keterangan' => ['nullable', 'string', 'max:500'],
            'attendance.*.waktu_absen' => ['nullable', 'date_format:H:i:s'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string> Custom validation messages
     */
    public function messages(): array
    {
        return [
            'attendance.required' => 'Data absensi wajib diisi.',
            'attendance.array' => 'Data absensi harus berupa array.',
            'attendance.min' => 'Minimal harus ada 1 data absensi.',
            'attendance.*.siswa_id.required' => 'Siswa wajib dipilih.',
            'attendance.*.siswa_id.exists' => 'Siswa tidak ditemukan.',
            'attendance.*.jadwal_pelajaran_id.required' => 'Jadwal pelajaran wajib dipilih.',
            'attendance.*.jadwal_pelajaran_id.exists' => 'Jadwal pelajaran tidak ditemukan.',
            'attendance.*.tanggal.required' => 'Tanggal wajib diisi.',
            'attendance.*.status.required' => 'Status kehadiran wajib dipilih.',
            'attendance.*.status.in' => 'Status harus salah satu dari: hadir, izin, sakit, alpha.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Adds the 'dicatat_oleh' field to each attendance record.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('attendance')) {
            $attendance = $this->input('attendance');

            foreach ($attendance as $key => $record) {
                $attendance[$key]['dicatat_oleh'] = $this->user()->id;
            }

            $this->merge(['attendance' => $attendance]);
        }
    }
}
