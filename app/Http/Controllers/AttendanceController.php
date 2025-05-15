<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    const MAX_DISTANCE_KM = 0.5; // 500 متر

    public function index()
    {
        $user = auth()->user();
        $from = request('from');
        $to = request('to');

        $query = $user->isAdminOrSuperAdmin() 
            ? Attendance::with(['user', 'branch'])
            : $user->attendances()->with(['branch']);

        if ($from && $to) {
            $query->whereBetween('date', [$from, $to]);
        }

        $attendances = $query->latest()->get();

        return view('dash.pages.attendance', compact('attendances'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + 
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        return $dist * 60 * 1.1515 * 1.609344; // كم
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        try {
            $branch = Branch::findOrFail($request->branch_id);

            if (is_null($branch->latitude)) {
                throw new \Exception("إحداثيات الفرع غير مضبوطة");
            }

            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $branch->latitude,
                $branch->longitude
            );

            Log::info('Attendance attempt', [
                'user_id' => $user->id,
                'distance' => $distance,
                'user_coords' => [$request->latitude, $request->longitude],
                'branch_coords' => [$branch->latitude, $branch->longitude]
            ]);

            $attendance = Attendance::firstOrNew([
                'user_id' => $user->id,
                'date' => $today
            ]);

            if (!$attendance->exists) {
                return $this->handleCheckIn($attendance, $request, $distance);
            } elseif (is_null($attendance->check_out)) {
                return $this->handleCheckOut($attendance, $request, $distance);
            }

            return back()->with('info', 'لقد قمت بتسجيل الحضور والانصراف مسبقاً اليوم.');

        } catch (\Exception $e) {
            Log::error('Attendance error: '.$e->getMessage());
            return back()->with('error', 'حدث خطأ: '.$e->getMessage());
        }
    }

    private function handleCheckIn($attendance, $request, $distance)
    {
        if ($distance > self::MAX_DISTANCE_KM) {
            throw new \Exception("يجب أن تكون داخل نطاق 500 متر من الفرع لتسجيل الحضور. المسافة الفعلية: ".round($distance, 2)." كم");
        }

        $attendance->fill([
            'branch_id' => $request->branch_id,
            'check_in' => Carbon::now(),
            'status' => 'In Progress',
            'location' => json_encode([
                'check_in' => [
                    'lat' => $request->latitude,
                    'lng' => $request->longitude,
                    'time' => Carbon::now()->toDateTimeString(),
                    'distance' => $distance
                ]
            ])
        ])->save();

        return back()->with('success', 'تم تسجيل الحضور بنجاح في فرع '.$attendance->branch->name);
    }

    private function handleCheckOut($attendance, $request, $distance)
    {
        if ($distance > self::MAX_DISTANCE_KM) {
            throw new \Exception("يجب أن تكون داخل نطاق 500 متر من الفرع لتسجيل الانصراف. المسافة الفعلية: ".round($distance, 2)." كم");
        }

        $locationData = json_decode($attendance->location, true);
        $locationData['check_out'] = [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
            'time' => Carbon::now()->toDateTimeString(),
            'distance' => $distance
        ];

        $attendance->update([
            'check_out' => Carbon::now(),
            'status' => 'Completed',
            'working_hours' => $this->calculateWorkingHours($attendance->check_in, Carbon::now()),
            'location' => json_encode($locationData)
        ]);

        return back()->with('success', 'تم تسجيل الانصراف بنجاح. وقت العمل: '.$attendance->working_hours.' ساعة');
    }

    private function calculateWorkingHours($checkIn, $checkOut)
    {
        return round(Carbon::parse($checkIn)->diffInMinutes(Carbon::parse($checkOut)) / 60, 2);
    }
}