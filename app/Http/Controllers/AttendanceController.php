<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $attendances = $user->isAdminOrSuperAdmin() 
            ? Attendance::with('user')->latest()->get()
            : $user->attendances()->latest()->get();
        
        return view('dash.pages.attendance', compact('attendances'));
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        
        // Check if attendance record exists for today
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
        
        if (!$attendance) {
            // Create check-in record
            Attendance::create([
                'user_id' => $user->id,
                'check_in' => Carbon::now(),
                'date' => $today,
            ]);
            
            return back()->with('success', 'Check-in recorded successfully');
        } elseif (!$attendance->check_out) {
            // Update check-out record
            $attendance->update([
                'check_out' => Carbon::now(),
            ]);
            
            return back()->with('success', 'Check-out recorded successfully');
        }
        
        return back()->with('error', 'You have already checked in and out today');
    }

    public function registerAttendance(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:in,out' // يمكن أن يكون نوع الحضور دخول أو خروج
        ]);

        $userLat = $request->latitude;
        $userLng = $request->longitude;
        $type = $request->type;

        // الحصول على جميع الفروع
        $branches = Branch::all();
        $nearestBranch = null;
        $minDistance = null;

        foreach ($branches as $branch) {
            // حساب المسافة بين الموظف والفرع
            $distance = $this->calculateDistance(
                $userLat, 
                $userLng, 
                $branch->latitude, 
                $branch->longitude
            );

            // إذا كانت المسافة ضمن نصف القطر المسموح به
            if ($distance <= $branch->attendance_radius) {
                // إذا كان هذا الفرع أقرب أو هو الفرع الأول الذي يفي بالشرط
                if (is_null($minDistance) || $distance < $minDistance) {
                    $minDistance = $distance;
                    $nearestBranch = $branch;
                }
            }
        }

        if ($nearestBranch) {
            // تسجيل الحضور
            Attendance::create([
                'user_id' => Auth::id(),
                'branch_id' => $nearestBranch->id,
                'type' => $type,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل حضورك من فرع ' . $nearestBranch->name
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'انت خارج نطاق أي فرع'
        ], 400);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // نصف قطر الأرض بالكيلومترات

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // المسافة بالكيلومترات

        return $distance * 1000; // تحويل إلى أمتار
    }
}