<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreKelasRequest
 *
 * Form request for validating class creation data.
 * Handles validation rules for creating new classes.
 *
 * @package App\Http\Requests
 */
class StoreKelasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if user is authorized, false otherwise
     */
    public function authorize(): bool
    {
        // Only admin can create classes
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string> Validation rules
     */
    public function rules(): array
    {
        return [
            'nama_kelas' => ['required', 'string', 'max:50', 'unique:kelas,nama_kelas'],
            'tingkat' => ['required', 'in:X,XI,XII'],
            'jurusan' => ['required', 'string', 'max:50'],
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
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'nama_kelas.unique' => 'Nama kelas sudah terdaftar.',
            'tingkat.required' => 'Tingkat kelas wajib dipilih.',
            'tingkat.in' => 'Tingkat harus salah satu dari: X, XI, XII.',
            'jurusan.required' => 'Jurusan wajib diisi.',
            'wali_kelas_id.exists' => 'Wali kelas tidak ditemukan.',
        ];
    }
}
