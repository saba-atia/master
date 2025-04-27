<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use App\Notifications\LeaveStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class LeaveController extends Controller
{
    // عرض الطلبات
    public function index()
    {
        if (Auth::user()->role === 'employee') {
            $leaves = Leave::where('user_id', Auth::id())->latest()->get();
        } else {
            // للمدير والمدير العام: عرض جميع الطلبات
            $leaves = Leave::with('user')->latest()->get();
        }

        return view('dash.pages.leaves', compact('leaves'));
    }

    // تقديم طلب جديد
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
             'reason' => 'required|string',
           ]);
    
        $leave = Leave::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'start_date' => $request->start_date, // تم التصحيح هنا
            'end_date' => $request->end_date,     // تم التصحيح هنا
            'reason' => $request->reason,
            'status' => Auth::user()->role === 'super_admin' ? 'approved' : 'pending',
        ]);

        // إرسال إشعار للمدير العام إذا كان الطلب من مدير
        if (Auth::user()->role === 'admin') {
            $superAdmins = User::where('role', 'super_admin')->get();
            Notification::send($superAdmins, new \App\Notifications\NewLeaveRequest($leave));
        }

        return redirect()->back()->with('success', 'Leave request submitted successfully.');
    }

    // تحديث حالة الإجازة (موافقة أو رفض) - للمدير العام فقط
    public function updateStatus(Request $request, $id)
    {
        if (Auth::user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'You are not authorized to perform this action.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $leave = Leave::findOrFail($id);
        $leave->status = $request->status;
        $leave->save();

        // إرسال إشعار للموظف
        $leave->user->notify(new LeaveStatusUpdated($leave));

        return redirect()->back()->with('success', 'Leave status updated successfully.');
    }
}