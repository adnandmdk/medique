<?php

namespace App\Http\Requests\Clinic;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClinicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:100', 'unique:clinics,name,' . $this->route('clinic')],
            'location'  => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama poliklinik wajib diisi.',
            'name.unique'       => 'Nama poliklinik sudah terdaftar.',
            'location.required' => 'Lokasi wajib diisi.',
        ];
    }
}