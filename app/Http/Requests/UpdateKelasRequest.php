<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateKelasRequest
 *
 * Form request for validating class update data.
 * Handles validation rules for updating existing classes.
 *
 * @package App\Http\Requests
 */
class UpdateKelasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if user is authorized, false otherwise
     */
    public function authorize(): bool
    {
        // Only admin can update classes
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> Validation rules
     */
    public function rules(): array
    {
        $kelasId = $this->route('kelas'); // Get kelas ID from route parameter

        return [
            'nama_kelas' => ['sometimes', 'string', 'max:50', Rule::unique('kelas', 'nama_kelas')->ignore($kelasId)],
            'tingkat' => ['sometimes', 'in:X,XI,XII'],
            'jurusan' => ['sometimes', 'string', 'max:50'],
            'wali_kelas_id' => ['nullable', 'integer', 'exists:users,id'],
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
            'nama_kelas.unique' => 'Nama kelas sudah terdaftar.',
            'tingkat.in' => 'Tingkat harus salah satu dari: X, XI, XII.',
            'wali_kelas_id.exists' => 'Wali kelas tidak ditemukan.',
        ];
    }
}
