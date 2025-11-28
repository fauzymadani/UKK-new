<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateAbsensiRequest
 *
 * Form request for validating attendance update data.
 * Handles validation rules for updating existing attendance records.
 *
 * @package App\Http\Requests
 */
class UpdateAbsensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if user is authorized, false otherwise
     */
    public function authorize(): bool
    {
        // Admin and guru can update attendance
        return $this->user() && in_array($this->user()->role, ['admin', 'guru']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> Validation rules
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'in:hadir,izin,sakit,alpha'],
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
            'status.in' => 'Status harus salah satu dari: hadir, izin, sakit, alpha.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'waktu_absen.date_format' => 'Format waktu absen harus H:i:s.',
        ];
    }
}
