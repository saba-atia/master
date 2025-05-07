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
        $query = Leave::with(['user', 'approver', 'department']);
    
        // تطبيق الفلتر حسب الحالة
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }
        
        // تطبيق الفلتر حسب النوع
        if (request()->has('type') && request('type') != '') {
            $query->where('type', request('type'));
        }
        
        // تطبيق الفلتر حسب الموظف (للمشرفين فقط)
        if (request()->has('user_id') && request('user_id') != '' && in_array($user->role, ['admin', 'super_admin'])) {
            $query->where('user_id', request('user_id'));
        }
        
        // تطبيق فلترة التواريخ
        if (request()->has('start_date') && request('start_date') != '') {
            $query->whereDate('start_time', '>=', request('start_date'));
        }
        
        if (request()->has('end_date') && request('end_date') != '') {
            $query->whereDate('end_time', '<=', request('end_date'));
        }
    
        // فلترة حسب الصلاحيات
        if ($user->role === 'department_manager') {
            // رئيس القسم يرى طلبات موظفي قسمه فقط
            $query->whereHas('user', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
            
            // لا يرى طلباته الشخصية إلا إذا كان هو مقدم الطلب
            $query->where('user_id', '!=', $user->id);
        } elseif (!in_array($user->role, ['admin', 'super_admin'])) {
            // الموظف العادي يرى طلباته فقط
            $query->where('user_id', $user->id);
        }
    
        $leaves = $query->latest()->paginate(10);
    
        // جلب قائمة الموظفين للفلتر
        $users = [];
        if (in_array($user->role, ['admin', 'super_admin', 'department_manager'])) {
            $usersQuery = User::query();
            
            if ($user->role === 'department_manager') {
                $usersQuery->where('department_id', $user->department_id)
                          ->where('id', '!=', $user->id);
            }
            
            $users = $usersQuery->get(['id', 'name']);
        }
    
        return view('dash.pages.leaves', [
            'leaves' => $leaves,
            'users' => $users,
            'message' => session('message')
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
                return back()->withErrors(['error' => 'You must be assigned to a department to submit leave requests']);
            }

            $start = new DateTime($validated['start_time']);
            $end = new DateTime($validated['end_time']);
            $diff = $end->diff($start);

            $duration = ($diff->days * 24) + $diff->h + ($diff->i / 60);

            if ($duration <= 0) {
                return back()->withErrors(['error' => 'End time must be after start time']);
            }

            Leave::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'type' => $validated['type'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration_hours' => $duration,
                'status' => 'pending',
                'reason' => $validated['reason'] ?? null
            ]);

            return redirect()->route('leaves.index')
                ->with('success', 'Leave request submitted successfully.')
                ->with('toast', [
                    'type' => 'success',
                    'message' => 'Your leave request has been submitted and is pending approval.'
                ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to submit leave request: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);
        $user = Auth::user();
    
        if ($user->role === 'department_manager') {
            if ($leave->department_id !== $user->department_id) {
                abort(403, 'Not authorized to approve leaves for this department');
            }
            $validStatuses = ['approved', 'rejected'];
        } elseif ($user->role === 'super_admin') {
            // للمشرفين فقط يمكنهم الموافقة على طلبات المديرين
            $validStatuses = ['approved', 'rejected'];
        } else {
            abort(403, 'Not authorized to approve leaves');
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
    
        return back()->with('success', 'Leave request updated successfully.');
    }
    public function show($id)
    {
        $leave = Leave::with(['user', 'approver', 'department'])->findOrFail($id);
        return response()->json($leave);
    }
}
