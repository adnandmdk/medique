<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'user_id'        => ['required', 'exists:users,id', 'unique:doctors,user_id'],
            'clinic_id'      => ['required', 'exists:clinics,id'],
            'specialization' => ['required', 'string', 'max:100'],
            'licence_number' => ['required', 'string', 'max:50', 'unique:doctors,licence_number'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'        => 'User wajib dipilih.',
            'user_id.exists'          => 'User tidak ditemukan.',
            'user_id.unique'          => 'User ini sudah terdaftar sebagai dokter.',
            'clinic_id.required'      => 'Poliklinik wajib dipilih.',
            'clinic_id.exists'        => 'Poliklinik tidak ditemukan.',
            'specialization.required' => 'Spesialisasi wajib diisi.',
            'licence_number.required' => 'Nomor lisensi wajib diisi.',
            'licence_number.unique'   => 'Nomor lisensi sudah terdaftar.',
        ];
    }
}