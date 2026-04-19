<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'doctor_id'   => ['required', 'exists:doctors,id'],
            'day_of_week' => [
                'required',
                Rule::in(['monday','tuesday','wednesday','thursday','friday','saturday','sunday']),
                // Satu dokter tidak boleh punya jadwal duplikat di hari yang sama
                Rule::unique('schedules')->where(function ($query) {
                    return $query->where('doctor_id', $this->doctor_id);
                }),
            ],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.required'   => 'Dokter wajib dipilih.',
            'doctor_id.exists'     => 'Dokter tidak ditemukan.',
            'day_of_week.required' => 'Hari wajib dipilih.',
            'day_of_week.in'       => 'Hari tidak valid.',
            'day_of_week.unique'   => 'Dokter ini sudah memiliki jadwal di hari tersebut.',
            'start_time.required'  => 'Jam mulai wajib diisi.',
            'start_time.date_format' => 'Format jam mulai tidak valid (HH:MM).',
            'end_time.required'    => 'Jam selesai wajib diisi.',
            'end_time.date_format' => 'Format jam selesai tidak valid (HH:MM).',
            'end_time.after'       => 'Jam selesai harus setelah jam mulai.',
        ];
    }
}