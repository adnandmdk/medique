<?php

namespace App\Http\Requests\Queue;

use Illuminate\Foundation\Http\FormRequest;

class StoreQueueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('patient');
    }

    public function rules(): array
    {
        return [
            'schedule_id'  => ['required', 'exists:schedules,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required'  => 'Jadwal wajib dipilih.',
            'schedule_id.exists'    => 'Jadwal tidak ditemukan.',
            'booking_date.required' => 'Tanggal booking wajib diisi.',
            'booking_date.date'     => 'Format tanggal tidak valid.',
            'booking_date.after_or_equal' => 'Tanggal booking tidak boleh sebelum hari ini.',
        ];
    }
}