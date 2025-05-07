<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. التحقق من تسجيل الدخول أولاً
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = Auth::user();

        // 2. تحديد الأدوار المسموح لها بالوصول
        $allowedRoles = ['super_admin', 'admin', 'department_manager'];

        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Unauthorized access. Your role: ' . $user->role);
        }

        // 3. تحسينات إضافية لرؤساء الأقسام
        if ($user->role === 'department_manager' && empty($user->department_id)) {
            abort(403, 'Department managers must be assigned to a department');
        }

        return $next($request);
    }
}