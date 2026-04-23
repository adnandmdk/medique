<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Hospital;
use App\Models\Queue;
use Illuminate\View\View;

class QueueDisplayController extends Controller
{
    public function index(): View
    {
        $hospitals = Hospital::where('is_active', true)->get();
        return view('display.select', compact('hospitals'));
    }

    public function show($clinicId)
{
    $clinic = Clinic::findOrFail($clinicId);

    $currentQueue = Queue::with([
    'patient',
    'schedule.doctor.user',
    'schedule.doctor.clinic',
])
->whereHas('schedule.doctor.clinic', function ($q) use ($clinic) {
    $q->where('id', $clinic->id);
})
->whereIn('status', ['called', 'in_progress'])
->orderBy('queue_number')
->first();

    $nextQueues = Queue::with(['patient'])
    ->whereHas('schedule.doctor.clinic', function ($q) use ($clinic) {
        $q->where('id', $clinic->id);
    })
    ->where('status', 'waiting')
    ->orderBy('queue_number')
    ->limit(5)
    ->get();
    $stats = [
    'waiting' => Queue::whereHas('schedule.doctor.clinic', fn($q) => $q->where('id',$clinic->id))->where('status','waiting')->count(),
    'done' => Queue::whereHas('schedule.doctor.clinic', fn($q) => $q->where('id',$clinic->id))->where('status','done')->count(),
    'total' => Queue::whereHas('schedule.doctor.clinic', fn($q) => $q->where('id',$clinic->id))->count(),
];

    return view('display.queue-display', compact(
        'clinic',
        'currentQueue',
        'nextQueues',
        'stats'
    ));
}
}