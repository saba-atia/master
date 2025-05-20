<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * عرض سجلات الحضور
     */
    public function index()
    {
        $user = auth()->user();
        $from = request('from');
        $to = request('to');
        $department_id = request('department_id');

        // استعلام الحضور (للموظف أو للمدير/المشرف)
        $query = $user->isAdminOrSuperAdmin() 
            ? Attendance::with(['user', 'user.department'])
            : $user->attendances()->with(['user.department']);

        // تطبيق الفلترة حسب التاريخ
        if ($from && $to) {
            $query->whereBetween('date', [$from, $to]);
        }

        // فلترة حسب القسم (للمديرين فقط)
        if ($department_id && $user->isAdminOrSuperAdmin()) {
            $query->whereHas('user', function ($q) use ($department_id) {
                $q->where('department_id', $department_id);
            });
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        // الموظفين الغائبين (للمديرين فقط)
        $absentEmployees = [];
        if ($user->isAdminOrSuperAdmin()) {
            $absentQuery = User::whereDoesntHave('attendances', function ($q) use ($from, $to) {
                if ($from && $to) {
                    $q->whereBetween('date', [$from, $to]);
                } else {
                    $q->whereDate('date', today());
                }
            });

            if ($department_id) {
                $absentQuery->where('department_id', $department_id);
            }

            $absentEmployees = $absentQuery->get();
        }

        return view('dash.pages.attendance', compact('attendances', 'absentEmployees'));
    }

    /**
     * تسجيل الحضور/الانصراف
     */
    public function store(Request $request)
{
    $user = Auth::user();
    $today = Carbon::today();
    $requiredHours = 8; // عدد الساعات المطلوبة يومياً

    try {
        // البحث عن سجل الحضور لهذا اليوم أو إنشاء جديد
        $attendance = Attendance::firstOrNew([
            'user_id' => $user->id,
            'date' => $today
        ]);

        if (!$attendance->exists) {
            // تسجيل الحضور (Check-in)
            $attendance->fill([
                'check_in' => Carbon::now(),
                'status' => 'In Progress'
            ])->save();

            return back()->with('success', '✅ تم تسجيل الحضور بنجاح | ' . now()->format('h:i A'));

        } elseif (!$attendance->check_out) {
            // تسجيل الانصراف (Check-out)
            $checkOutTime = Carbon::now();
            
            // حساب ساعات العمل بدقة
            $workingHours = $this->calculateWorkingHours($attendance->check_in, $checkOutTime);
            
            // تحديث سجل الحضور
            $attendance->update([
                'check_out' => $checkOutTime,
                'working_hours' => $workingHours,
                'status' => $workingHours >= $requiredHours ? 'Completed' : 'Incomplete'
            ]);

            // رسالة مختلفة حسب حالة إكمال الساعات
            if ($workingHours >= $requiredHours) {
                $message = '✅ تم تسجيل الانصراف | يوم عمل مكتمل (' . number_format($workingHours, 2) . ' ساعة)';
                $alertType = 'success';
            } else {
                $message = '⚠️ تم تسجيل الانصراف | يوم عمل غير مكتمل (' . number_format($workingHours, 2) . ' ساعة)';
                $alertType = 'warning';
            }

            return back()->with($alertType, $message);

        } else {
            return back()->with('info', 'ℹ️ لقد سجلت الحضور والانصراف مسبقاً لهذا اليوم');
        }

    } catch (\Exception $e) {
        Log::error('Attendance Error: ' . $e->getMessage());
        return back()->with('error', '❌ حدث خطأ: ' . $e->getMessage());
    }
}

    /**
     * تحديث الصورة الشخصية
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $user = auth()->user();
            $path = $request->file('avatar')->store('users/avatars', 'public');
            
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
            }

            $user->update(['photo_url' => $path]);
            return back()->with('success', 'تم تحديث الصورة الشخصية بنجاح');

        } catch (\Exception $e) {
            Log::error('Avatar Update Error: ' . $e->getMessage());
            return back()->with('error', 'فشل تحديث الصورة: ' . $e->getMessage());
        }
    }

    /**
     * حساب ساعات العمل بدقة
     */
/**
 * حساب ساعات العمل بدقة
 */
/**
 * حساب ساعات العمل بدقة
 */
private function calculateWorkingHours($checkIn, $checkOut)
{
    try {
        // تحويل الأوقات إلى كائنات Carbon
        $start = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);
        
        // حساب الفرق بالثواني لضمان الدقة
        $totalSeconds = $end->diffInSeconds($start);
        
        // التحويل إلى ساعات مع الكسور
        $hours = $totalSeconds / 3600;
        
        // تقريب إلى منزلتين عشريتين
        return round($hours, 2);
        
    } catch (\Exception $e) {
        Log::error('خطأ في حساب ساعات العمل: ' . $e->getMessage());
        return 0;
    }
}

    // ─── الدوال الإضافية ───────────────────────────────────────────────────────

    /**
     * عرض الغائبين عن اليوم
     */
    public function todaysAbsentees()
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            abort(403);
        }

        $absentees = User::whereDoesntHave('attendances', function ($q) {
            $q->whereDate('date', today());
        })->get();

        return view('dash.pages.absentees', compact('absentees'));
    }

    /**
     */
   
}