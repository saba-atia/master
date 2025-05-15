<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    // دالة مساعدة لتوليد كلمة المرور
    private function generateAutoPassword($name)
    {
        // تنظيف الاسم من المسافات والأحرف الخاصة
        $cleanedName = preg_replace('/[^A-Za-z]/', '', $name);
        
        // إذا كان الاسم فارغاً بعد التنظيف نستخدم قيمة افتراضية
        if (empty($cleanedName)) {
            $cleanedName = 'User';
        }
        
        // أول حرف كبير وبقية الأحرف صغيرة
        $formattedName = ucfirst(strtolower($cleanedName));
        
        // إنشاء كلمة المرور بالصيغة المطلوبة
        return $formattedName . '@123';
    }

    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'super_admin') {
            $employees = User::with('department')->get();
        }
        elseif ($user->role === 'admin') {
            $employees = User::with('department')->get();
        }
        elseif ($user->role === 'department_manager') {
            $employees = User::where('department_id', $user->department_id)
                           ->with('department')
                           ->get();
        }
        else {
            abort(403, 'Unauthorized action.');
        }
    
        $departments = Department::all();
        return view('admin.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::all();
        $roles = ['employee', 'department_manager'];
        
        if ($user->role === 'super_admin') {
            $roles[] = 'admin';
        }

        return view('admin.employees.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users',
            'department_id' => 'required|exists:departments,id',
            'role' => 'required|in:employee,admin,department_manager'
        ]);

        if ($user->role === 'admin' && $request->role === 'admin') {
            return back()->with('error', 'You cannot create another admin');
        }

        // توليد كلمة المرور التلقائية
        $autoPassword = $this->generateAutoPassword($request->name);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($autoPassword),
            'department_id' => $request->department_id,
            'role' => $request->role
        ]);

        return redirect()->route('admin.employees.index')
               ->with([
                   'success' => 'Employee added successfully',
                   'generated_password' => 'Auto generated password: ' . $autoPassword
               ]);
    }

    public function edit(User $employee)
    {
        $user = auth()->user();
        
        if ($user->role === 'super_admin') {
            // يمكنه تعديل الجميع
        }
        elseif ($user->role === 'admin') {
            if ($employee->role === 'super_admin') {
                abort(403, 'Unauthorized action.');
            }
        }
        elseif ($user->role === 'department_manager') {
            if ($employee->department_id !== $user->department_id) {
                abort(403, 'Unauthorized action.');
            }
        }
        else {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::all();
        $roles = ['employee', 'department_manager'];
        
        if ($user->role === 'super_admin') {
            $roles[] = 'admin';
        }

        return view('admin.employees.edit', compact('employee', 'departments', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        $user = auth()->user();
        
        if ($user->role === 'admin' && $employee->role === 'super_admin') {
            abort(403, 'Unauthorized action.');
        }
        if ($user->role === 'department_manager' && $employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$employee->id,
            'department_id' => 'required|exists:departments,id',
            'role' => 'required|in:employee,admin,department_manager',
            'password' => 'nullable|min:8|confirmed'
        ]);

        if ($user->role === 'admin' && $validated['role'] === 'admin' && $employee->role !== 'admin') {
            return back()->with('error', 'You cannot change role to admin');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
            'role' => $validated['role']
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $employee->update($data);
        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully');
    }

    public function destroy(User $employee)
    {
        $user = auth()->user();
        
        if ($user->role === 'super_admin') {
            $employee->delete();
            return redirect()->back()->with('success', 'Employee deleted successfully');
        }
        
        if ($user->role === 'admin') {
            if (in_array($employee->role, ['super_admin', 'admin'])) {
                return redirect()->back()->with('error', 'You cannot delete this employee');
            }
            $employee->delete();
            return redirect()->back()->with('success', 'Employee deleted successfully');
        }
        
        if ($user->role === 'department_manager' && $employee->department_id === $user->department_id) {
            $employee->delete();
            return redirect()->back()->with('success', 'Employee deleted successfully');
        }
        
        return redirect()->back()->with('error', 'You are not authorized to delete this employee');
    }
}