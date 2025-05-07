<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
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
        
        // فقط السوبر أدمن والأدمن يمكنهم الإضافة
        if (!in_array($user->role, ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::all();
        $roles = ['employee', 'department_manager']; // الأدمن لا يمكنه إضافة أدمن آخر
        
        // السوبر أدمن فقط يمكنه إضافة أدمن
        if ($user->role === 'super_admin') {
            $roles[] = 'admin';
        }

        return view('admin.employees.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // التحقق من الصلاحية
        if (!in_array($user->role, ['super_admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'department_id' => 'required|exists:departments,id',
            'role' => 'required|in:employee,admin,department_manager'
        ]);

        // منع الأدمن من إنشاء أدمن آخر
        if ($user->role === 'admin' && $request->role === 'admin') {
            return back()->with('error', 'You cannot create another admin');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'role' => $request->role
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Employee added successfully');
    }

    public function edit(User $employee)
    {
        $user = auth()->user();
        
        // السوبر أدمن يمكنه تعديل أي موظف
        if ($user->role === 'super_admin') {
            // يمكنه تعديل الجميع
        }
        // الأدمن يمكنه تعديل الجميع ما عدا السوبر أدمن
        elseif ($user->role === 'admin') {
            if ($employee->role === 'super_admin') {
                abort(403, 'Unauthorized action.');
            }
        }
        // رئيس القسم يمكنه تعديل موظفي قسمه فقط
        elseif ($user->role === 'department_manager') {
            if ($employee->department_id !== $user->department_id) {
                abort(403, 'Unauthorized action.');
            }
        }
        // غير ذلك لا يسمح
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
        
        // نفس شروط التعديل
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

        // منع الأدمن من تحويل أي شخص إلى أدمن
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
        
        // السوبر أدمن يمكنه حذف أي موظف
        if ($user->role === 'super_admin') {
            $employee->delete();
            return redirect()->back()->with('success', 'Employee deleted successfully');
        }
        
        // الأدمن يمكنه حذف أي موظف ما عدا السوبر أدمن والأدمن الآخر
        if ($user->role === 'admin') {
            if (in_array($employee->role, ['super_admin', 'admin'])) {
                return redirect()->back()->with('error', 'You cannot delete this employee');
            }
            $employee->delete();
            return redirect()->back()->with('success', 'Employee deleted successfully');
        }
        
        // رئيس القسم يمكنه حذف موظفي قسمه فقط
        if ($user->role === 'department_manager' && $employee->department_id === $user->department_id) {
            $employee->delete();
            return redirect()->back()->with('success', 'Employee deleted successfully');
        }
        
        return redirect()->back()->with('error', 'You are not authorized to delete this employee');
    }
}