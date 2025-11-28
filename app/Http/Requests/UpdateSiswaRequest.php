<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateSiswaRequest
 *
 * Form request for validating student update data.
 * Handles validation rules for updating existing students.
 *
 * @package App\Http\Requests
 */
class UpdateSiswaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if user is authorized, false otherwise
     */
    public function authorize(): bool
    {
        // Only admin and guru can update students
        return $this->user() && in_array($this->user()->role, ['admin', 'guru']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> Validation rules
     */
    public function rules(): array
    {
        $siswaId = $this->route('siswa'); // Get siswa ID from route parameter

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($siswaId, 'id')],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'nis' => ['sometimes', 'string', 'max:20', Rule::unique('siswa', 'nis')->ignore($siswaId)],
            'nisn' => ['sometimes', 'string', 'max:20', Rule::unique('siswa', 'nisn')->ignore($siswaId)],
            'kelas_id' => ['sometimes', 'integer', 'exists:kelas,id'],
            'jenis_kelamin' => ['sometimes', 'in:L,P'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_telp' => ['nullable', 'string', 'max:15'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
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
            'name.string' => 'Nama harus berupa teks.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
