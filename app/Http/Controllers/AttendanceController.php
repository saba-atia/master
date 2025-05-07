<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdminOrSuperAdmin()) {
            $attendances = Attendance::with(['user' => function($query) {
                $query->withTrashed();
            }, 'branch'])
            ->latest()
            ->get();
        } else {
            $attendances = $user->attendances()
                ->with('branch')
                ->latest()
                ->get();
        }
    
        return view('dash.pages.attendance', compact('attendances'));
    }
    
    public function create()
    {
        $branches = Branch::all();
        return view('admin.attendance.create', compact('branches'));
    }
    
    public function store(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
    
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);
    
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
    
        if (!$attendance) {
            Attendance::create([
                'user_id' => $user->id,
                'branch_id' => $request->branch_id,
                'check_in' => Carbon::now(),
                'date' => $today,
                'status' => 'In Progress'
            ]);
    
            return back()->with('success', 'Check-in recorded successfully');
        } elseif (!$attendance->check_out) {
            $attendance->update([
                'check_out' => Carbon::now(),
                'status' => 'Completed',
                'working_hours' => $this->calculateWorkingHours($attendance->check_in, Carbon::now())
            ]);
    
            return back()->with('success', 'Check-out recorded successfully');
        }
    
        return back()->with('error', 'Attendance already recorded for today');
    }

    private function calculateWorkingHours($checkIn, $checkOut)
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);
        
        return round($checkOut->diffInMinutes($checkIn) / 60, 2);
    }
}