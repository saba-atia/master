<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use PDF;
use Excel;
use App\Exports\EvaluationsExport;
use Carbon\Carbon;

class EvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,super admin');
    }

    public function index()
    {
        $evaluations = Evaluation::with(['user', 'user.attendances' => function($query) {
            $query->whereMonth('date', now()->month);
        }])
        ->when(request('search'), function($query) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%'.request('search').'%');
            });
        })
        ->when(request('date'), function($query) {
            $query->whereDate('evaluation_date', request('date'));
        })
        ->latest()
        ->paginate(10);

        return view('evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $this->authorize('create', Evaluation::class);
        
        $users = User::with(['attendances' => function($query) {
            $query->whereMonth('date', now()->month);
        }])->get();
        
        return view('evaluations.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Evaluation::class);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'evaluation_date' => 'required|date',
            'work_quality' => 'required|integer|min:1|max:10',
            'teamwork' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string'
        ]);

        // حساب الالتزام تلقائياً من سجلات الحضور
        $punctualityScore = $this->calculatePunctualityScore($request->user_id);
        
        $evaluation = Evaluation::create(array_merge($validated, [
            'punctuality' => $punctualityScore
        ]));

        return redirect()->route('evaluations.index')
            ->with('success', 'تم إضافة التقييم بنجاح');
    }

    public function show(Evaluation $evaluation)
    {
        $this->authorize('view', $evaluation);
        
        // جلب سجلات الحضور المرتبطة
        $attendances = $evaluation->user->attendances()
            ->whereBetween('date', [
                $evaluation->evaluation_date->startOfMonth(),
                $evaluation->evaluation_date->endOfMonth()
            ])
            ->get();
            
        return view('evaluations.show', compact('evaluation', 'attendances'));
    }

    public function edit(Evaluation $evaluation)
    {
        $this->authorize('update', $evaluation);
        
        $users = User::all();
        $attendances = $evaluation->user->attendances()
            ->whereBetween('date', [
                $evaluation->evaluation_date->startOfMonth(),
                $evaluation->evaluation_date->endOfMonth()
            ])
            ->get();
            
        return view('evaluations.edit', compact('evaluation', 'users', 'attendances'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $this->authorize('update', $evaluation);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'evaluation_date' => 'required|date',
            'work_quality' => 'required|integer|min:1|max:10',
            'teamwork' => 'required|integer|min:1|max:10',
            'notes' => 'nullable|string'
        ]);

        // تحديث درجة الالتزام تلقائياً
        $punctualityScore = $this->calculatePunctualityScore($request->user_id);
        
        $evaluation->update(array_merge($validated, [
            'punctuality' => $punctualityScore
        ]));

        return redirect()->route('evaluations.show', $evaluation->id)
            ->with('success', 'تم تحديث التقييم بنجاح');
    }

    public function destroy(Evaluation $evaluation)
    {
        $this->authorize('delete', $evaluation);
        
        $evaluation->delete();
        
        return redirect()->route('evaluations.index')
            ->with('success', 'تم حذف التقييم بنجاح');
    }

    /**
     * حساب درجة الالتزام من سجلات الحضور
     */
    private function calculatePunctualityScore($userId)
    {
        $user = User::with(['attendances' => function($query) {
            $query->whereMonth('date', now()->month);
        }])->findOrFail($userId);

        $totalDays = $user->attendances->count();
        $onTimeDays = $user->attendances->filter(function($attendance) {
            return $attendance->status == 'present' && 
                   $attendance->arrival_time <= $attendance->expected_start_time;
        })->count();

        if ($totalDays > 0) {
            $percentage = ($onTimeDays / $totalDays) * 100;
            return min(10, max(1, round($percentage / 10))); // تحويل النسبة إلى مقياس 1-10
        }

        return 5; // القيمة الافتراضية إذا لم توجد سجلات
    }

    private function calculatePunctuality($userId) {
    $attendanceRecords = Attendance::where('user_id', $userId)
        ->whereMonth('date', now()->month)
        ->get();
    
    $totalDays = $attendanceRecords->count();
    $onTimeDays = $attendanceRecords->filter(fn($record) => 
        $record->status == 'present' && 
        $record->arrival_time <= $record->shift->start_time
    )->count();

    return ($totalDays > 0) ? round(($onTimeDays / $totalDays) * 10) : 0;
}
}