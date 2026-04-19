<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorAttendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // Toggle hadir/libur hari ini
    public function toggle(Request $request)
    {
        $doctor = $request->user()->doctor;

        if (! $doctor) {
            return back()->with('error', 'Data dokter tidak ditemukan.');
        }

        $att = DoctorAttendance::firstOrCreate(
            ['doctor_id' => $doctor->id, 'date' => today()->toDateString()],
            ['is_present' => true]
        );

        // Jika sudah ada, toggle
        if (! $att->wasRecentlyCreated) {
            $att->update(['is_present' => ! $att->is_present]);
        }

        $status = $att->fresh()->is_present ? 'Hadir' : 'Libur';

        return back()->with('success', "Status hari ini diperbarui: {$status}");
    }
}