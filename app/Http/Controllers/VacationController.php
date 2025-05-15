<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Vacation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Notifications\VacationStatusChanged;

class VacationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Vacation::with(['user' => function($q) {
        }, 'approver', 'department']);
    
        // تطبيق الفلاتر
        $this->applyFilters($query, $user);
    
        $vacations = $query->latest()->paginate(10);
        $users = $this->getFilterableUsers($user);
    
        return view('vacations.index', [
            'vacations' => $vacations,
            'users' => $users,
            'message' => session('message')
        ]);
    }

    private function applyFilters($query, $user)
    {
        // فلترة حسب الحالة
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        // فلترة حسب النوع
        if (request('type')) {
            $query->where('type', request('type'));
        }
        
        // فلترة حسب الموظف (للمشرفين فقط)
        if (request('user_id') && in_array($user->role, ['admin', 'super_admin', 'department_manager'])) {
            $query->where('user_id', request('user_id'));
        }
        
        // فلترة حسب التواريخ
        if (request('start_date')) {
            $query->whereDate('start_date', '>=', request('start_date'));
        }
        
        if (request('end_date')) {
            $query->whereDate('end_date', '<=', request('end_date'));
        }
    
        // فلترة حسب الصلاحيات
        if ($user->role === 'department_manager') {
            $query->whereHas('user', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        } elseif (!in_array($user->role, ['admin', 'super_admin'])) {
            $query->where('user_id', $user->id);
        }
    }

    private function getFilterableUsers($user)
    {
        if (!in_array($user->role, ['admin', 'super_admin', 'department_manager'])) {
            return collect();
        }

        return User::when($user->role === 'department_manager', function($q) use ($user) {
                $q->where('department_id', $user->department_id)
                  ->where('role', '!=', 'admin');
            })
            ->when($user->role === 'admin', function($q) {
                $q->where('role', 'employee');
            })
            ->when($user->role === 'super_admin', function($q) {
                $q->whereIn('role', ['admin', 'department_manager', 'employee']);
            })
            ->get(['id', 'name', 'photo_url']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:annual,sick,unpaid,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        if (!$user->department_id) {
            return back()->withErrors(['error' => 'You must be assigned to a department to submit vacation requests']);
        }

        $days = $this->calculateWorkingDays($validated['start_date'], $validated['end_date']);

        $vacation = Vacation::create([
            'user_id' => $user->id,
            'department_id' => $user->department_id,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_taken' => $days,
            'reason' => $validated['reason'],
            'status' => 'pending'
        ]);

        return redirect()->route('vacations.index')
            ->with('toast', [
                'type' => 'success',
                'message' => 'Vacation request submitted successfully'
            ]);
    }

    private function calculateWorkingDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        return $start->diffInDaysFiltered(function(Carbon $date) {
            return !$date->isWeekend();
        }, $end) + 1;
    }

    public function update(Request $request, $id)
    {
        $vacation = Vacation::with('user')->findOrFail($id);
        $user = Auth::user();

        $validStatuses = $this->getValidStatuses($user, $vacation);
        
        if (empty($validStatuses)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'You are not authorized to update this vacation request'
            ]);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in($validStatuses)],
            'notes' => 'nullable|string|max:500'
        ]);

        $vacation->update([
            'status' => $validated['status'],
            'approved_by' => $user->id,
            'notes' => $validated['notes'] ?? null
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Vacation status updated'
        ]);
    }

    private function getValidStatuses($user, $vacation)
    {
        if ($user->role === 'department_manager' && 
            $vacation->user->department_id === $user->department_id &&
            $vacation->user->id !== $user->id &&
            $vacation->user->role !== 'admin') {
            return ['approved', 'rejected'];
        }

        if ($user->role === 'admin' && $vacation->user->role !== 'admin') {
            return ['approved', 'rejected'];
        }

        if ($user->role === 'super_admin') {
            return ['approved', 'rejected'];
        }

        return [];
    }

    public function show($id)
    {
        $vacation = Vacation::with(['user', 'approver', 'department'])->find($id);
        
        if (!$vacation) {
            return response()->json(['error' => 'Vacation not found'], 404);
        }

        return response()->json([
            'id' => $vacation->id,
            'user' => [
                'name' => $vacation->user->name,
                'role' => $vacation->user->role,
                'photo_url' => $vacation->user->photo_url ? asset('storage/'.$vacation->user->photo_url) : null
            ],
            'department' => [
                'name' => $vacation->department->name ?? null
            ],
            'approver' => $vacation->approver ? [
                'name' => $vacation->approver->name
            ] : null,
            'type' => $vacation->type,
            'status' => $vacation->status,
            'start_date' => $vacation->start_date,
            'end_date' => $vacation->end_date,
            'days_taken' => $vacation->days_taken,
            'reason' => $vacation->reason,
            'notes' => $vacation->notes,
            'created_at' => $vacation->created_at,
            'updated_at' => $vacation->updated_at
        ]);
    }
}