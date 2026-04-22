<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Queue;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HospitalController extends Controller
{
    public function index(): View
    {
        $hospitals = Hospital::withCount(['clinics','doctors','queues' => fn($q) =>
            $q->where('booking_date', today())
        ])->get();

        return view('admin.hospitals.index', compact('hospitals'));
    }

    public function show(Hospital $hospital): View
    {
        $hospital->load([
            'clinics' => fn($q) => $q->withCount('doctors'),
        ]);

        $stats = [
            'total_doctors'  => Doctor::where('hospital_id', $hospital->id)->count(),
            'total_clinics'  => Clinic::where('hospital_id', $hospital->id)->count(),
            'today_queues'   => Queue::where('hospital_id', $hospital->id)->where('booking_date', today())->count(),
            'waiting_queues' => Queue::where('hospital_id', $hospital->id)->where('status', 'waiting')->count(),
        ];

        $doctors = Doctor::with(['user','clinic','schedules'])
            ->where('hospital_id', $hospital->id)
            ->get();

        $queues = Queue::with(['patient','schedule.doctor.user','schedule.doctor.clinic'])
            ->where('hospital_id', $hospital->id)
            ->where('booking_date', today())
            ->orderBy('queue_number')
            ->get();

        return view('admin.hospitals.show', compact('hospital','stats','doctors','queues'));
    }

    public function create(): View
    {
        return view('admin.hospitals.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'code'    => 'required|string|max:10|unique:hospitals,code',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email',
            'tagline' => 'nullable|string|max:100',
        ]);

        Hospital::create($data);

        return redirect()->route('admin.hospitals.index')
            ->with('success', 'Rumah sakit berhasil ditambahkan.');
    }

    public function edit(Hospital $hospital): View
    {
        return view('admin.hospitals.edit', compact('hospital'));
    }

    public function update(Request $request, Hospital $hospital)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'code'    => 'required|string|max:10|unique:hospitals,code,'.$hospital->id,
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email',
            'tagline' => 'nullable|string|max:100',
        ]);

        $hospital->update($data);

        return redirect()->route('admin.hospitals.index')
            ->with('success', 'Data rumah sakit diperbarui.');
    }

    public function destroy(Hospital $hospital)
    {
        $hospital->delete();
        return redirect()->route('admin.hospitals.index')
            ->with('success', 'Rumah sakit dihapus.');
    }
}