<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreAbsensiRequest
 *
 * Form request for validating attendance recording data.
 * Handles validation rules for creating new attendance records.
 *
 * @package App\Http\Requests
 */
class StoreAbsensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if user is authorized, false otherwise
     */
    public function authorize(): bool
    {
        // Admin and guru can record attendance
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
            'siswa_id' => ['required', 'integer', 'exists:siswa,id'],
            'jadwal_pelajaran_id' => ['required', 'integer', 'exists:jadwal_pelajaran,id'],
            'tanggal' => ['required', 'date', 'date_format:Y-m-d'],
            'status' => ['required', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'waktu_absen' => ['nullable', 'date_format:H:i:s'],
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
            'siswa_id.required' => 'Siswa wajib dipilih.',
            'siswa_id.exists' => 'Siswa tidak ditemukan.',
            'jadwal_pelajaran_id.required' => 'Jadwal pelajaran wajib dipilih.',
            'jadwal_pelajaran_id.exists' => 'Jadwal pelajaran tidak ditemukan.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'tanggal.date_format' => 'Format tanggal harus Y-m-d.',
            'status.required' => 'Status kehadiran wajib dipilih.',
            'status.in' => 'Status harus salah satu dari: hadir, izin, sakit, alpha.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'waktu_absen.date_format' => 'Format waktu absen harus H:i:s.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Sets the 'dicatat_oleh' field to current authenticated user ID.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'dicatat_oleh' => $this->user()->id,
        ]);
    }
}
