<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Leave::with(['user' => function($q) {
            $q->with('department');
        }, 'approver', 'department']);

        // تحديد الطلبات بناء على الصلاحية
        if ($user->isDepartmentManager()) {
            // رئيس القسم يرى طلبات موظفي قسمه فقط
            $query->whereHas('user', function($q) use ($user) {
                $q->where('department_id', $user->department_id)
                  ->where('role', 'employee'); // فقط موظفي القسم وليس مدراء أو مديرين
            });
        } elseif ($user->isAdmin()) {
            // المدير يرى طلبات رؤساء الأقسام في قسمه
            $query->whereHas('user', function($q) use ($user) {
                $q->where('department_id', $user->department_id)
                  ->where('role', 'department_manager');
            });
        } elseif ($user->isSuperAdmin()) {
            // السوبر أدمن يرى طلبات المديرين
            $query->whereHas('user', function($q) {
                $q->where('role', 'admin');
            });
        } elseif (!$user->isSuperAdmin()) {
            // الموظف العادي يرى طلباته فقط
            $query->where('user_id', $user->id);
        }

        // تطبيق الفلاتر
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }

        if (request()->has('type') && request('type') != '') {
            $query->where('type', request('type'));
        }

        if (request()->has('user_id') && request('user_id') != '' && $user->canViewOtherUsers()) {
            $query->where('user_id', request('user_id'));
        }

        if (request()->has('start_date') && request('start_date') != '') {
            $query->whereDate('start_time', '>=', request('start_date'));
        }

        if (request()->has('end_date') && request('end_date') != '') {
            $query->whereDate('end_time', '<=', request('end_date'));
        }

        $leaves = $query->latest()->paginate(10);

        $users = [];
if (in_array($user->role, ['super_admin', 'admin', 'department_manager'])) {
            if ($user->isSuperAdmin()) {
                $users = User::where('role', 'admin')->get(['id', 'name']);
            } elseif ($user->isAdmin()) {
                $users = User::where('department_id', $user->department_id)
                            ->where('role', 'department_manager')
                            ->get(['id', 'name']);
            } elseif ($user->isDepartmentManager()) {
                $users = User::where('department_id', $user->department_id)
                            ->where('role', 'employee')
                            ->get(['id', 'name']);
            }
        }

        return view('dash.pages.leaves', [
            'leaves' => $leaves,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:personal,official',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $user = Auth::user();

            if (!$user->department_id) {
                return back()->with('toast', [
                    'type' => 'error',
                    'message' => 'You must be assigned to a department to submit leave requests'
                ]);
            }

            $start = new DateTime($validated['start_time']);
            $end = new DateTime($validated['end_time']);
            $diff = $end->diff($start);
            $duration = ($diff->days * 24) + $diff->h + ($diff->i / 60);

            if ($duration <= 0) {
                return back()->with('toast', [
                    'type' => 'error',
                    'message' => 'End time must be after start time'
                ]);
            }

            // تحديد حالة الطلب بناء على دور المستخدم
            $status = 'pending';
            if ($user->isSuperAdmin()) {
                $status = 'approved';
            } elseif ($user->isAdmin()) {
                $status = 'pending_super_admin';
            } elseif ($user->isDepartmentManager()) {
                $status = 'pending_admin';
            }

            Leave::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'type' => $validated['type'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_hours' => $duration,
                'status' => $status,
                'reason' => $validated['reason'] ?? null
            ]);

            return redirect()->route('leaves.index')->with('toast', [
                'type' => 'success',
                'message' => 'Your leave request has been submitted.'
            ]);

        } catch (\Exception $e) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Failed to submit leave request: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::with('user')->findOrFail($id);
        $user = Auth::user();

        // التحقق من الصلاحيات لتعديل حالة الطلب
        if ($user->isSuperAdmin() && $leave->user->isAdmin() && $leave->status === 'pending_super_admin') {
            $validStatuses = ['approved', 'rejected'];
        } elseif ($user->isAdmin() && $leave->user->isDepartmentManager() && $leave->department_id === $user->department_id && $leave->status === 'pending_admin') {
            $validStatuses = ['approved', 'rejected'];
        } elseif ($user->isDepartmentManager() && $leave->user->isRegularEmployee() && $leave->department_id === $user->department_id && $leave->status === 'pending') {
            $validStatuses = ['department_approved', 'rejected'];
        } else {
            abort(403, 'You are not authorized to approve this leave request');
        }

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $validStatuses),
            'notes' => 'nullable|string|max:500'
        ]);

        $leave->update([
            'status' => $validated['status'],
            'approved_by' => $user->id,
            'notes' => $validated['notes'] ?? null
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Request status updated successfully'
        ]);
    }

    public function show($id)
    {
        $leave = Leave::with(['user', 'approver', 'department'])->findOrFail($id);
        $user = Auth::user();

        // التحقق من الصلاحيات لعرض التفاصيل
        if ($user->isSuperAdmin() || 
            ($user->isAdmin() && $leave->department_id === $user->department_id) ||
            ($user->isDepartmentManager() && $leave->department_id === $user->department_id) ||
            $leave->user_id === $user->id) {
            return response()->json($leave);
        }

        abort(403, 'Unauthorized access');
    }
}