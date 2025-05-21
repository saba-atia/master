<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;

class EmployeeController extends Controller
{
    // توليد كلمة مرور تلقائية
    private function generateAutoPassword($name)
    {
        $cleanedName = preg_replace('/[^A-Za-z]/', '', $name);
        if (empty($cleanedName)) {
            $cleanedName = 'User';
        }
        return ucfirst(strtolower($cleanedName)) . '@123';
    }

    public function index()
    {
        $user = auth()->user();
        $query = User::with('department')->where('status', 'active');
        
        if ($user->isDepartmentManager()) {
            $query->where('department_id', $user->department_id);
        }
        
        $employees = $query->get();
        $departments = Department::all();
        $inactiveCount = User::where('status', 'inactive')->count();
        
        return view('admin.employees.index', compact('employees', 'departments', 'inactiveCount'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::all();
        $roles = ['employee', 'department_manager'];
        
        if ($user->isSuperAdmin()) {
            $roles[] = 'admin';
        }

        return view('admin.employees.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
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

        $autoPassword = $this->generateAutoPassword($request->name);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($autoPassword),
            'department_id' => $request->department_id,
            'role' => $request->role,
            'status' => 'active'
        ]);

        return redirect()->route('admin.employees.index')
               ->with([
                   'success' => 'User added successfully',
                   'generated_password' => 'Auto generated password: ' . $autoPassword
               ]);
    }

    public function show(User $employee)
    {
        $this->authorize('view', $employee);
        
        return view('admin.employees.show', [
            'employee' => $employee->load('department'),
            'lastLogin' => $employee->last_login_at?->format('M d, Y H:i'),
            'activities' => Activity::where('causer_id', $employee->id)
                                ->latest()
                                ->take(10)
                                ->get()
        ]);
    }

    public function edit(User $employee)
    {
        $user = auth()->user();
        
        if ($user->isDepartmentManager() && $employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->isAdmin() && $employee->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::all();
        $roles = ['employee', 'department_manager'];
        
        if ($user->isSuperAdmin()) {
            $roles[] = 'admin';
        }

        return view('admin.employees.edit', compact('employee', 'departments', 'roles'));
    }
    

    public function update(Request $request, User $employee)
    {
        $user = auth()->user();
        
        if ($user->isAdmin() && $employee->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($user->isDepartmentManager() && $employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$employee->id,
            'department_id' => 'required|exists:departments,id',
            'role' => 'required|in:employee,admin,department_manager',
            'password' => 'nullable|min:8|confirmed'
        ]);

        if ($user->isAdmin() && $validated['role'] === 'admin' && !$employee->isAdmin()) {
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
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($employee)
            ->log('updated employee profile');

        return redirect()->route('admin.employees.index')->with('success', 'User updated successfully');
    }

public function destroy(User $employee)
{
    $user = auth()->user();

    // السوبر أدمن يمكنه حذف أي مستخدم ما عدا نفسه
    if ($user->isSuperAdmin() && $employee->id !== $user->id) {
        $employee->delete(); // استخدام delete() بدلاً من update للحذف الفعلي
        return redirect()->route('admin.employees.inactive')->with('success', "User {$employee->name} deleted successfully!");
    }

    // الأدمن يمكنه حذف موظفين عاديين أو مدراء أقسام
    if ($user->isAdmin() && !$employee->isAdmin() && !$employee->isSuperAdmin()) {
        $employee->delete();
        return redirect()->route('admin.employees.inactive')->with('success', "User {$employee->name} deleted successfully!");
    }

    return redirect()->back()->with('error', 'You are not authorized to delete this user!');
}

public function inactiveEmployees()
{
    // استبدال authorization بالشرط المباشر (اختياري)
    if (!auth()->user()->isAdmin()) {
        abort(403);
    }
    
    $inactiveEmployees = User::where('status', 'inactive')
        ->with(['department'])
        ->orderBy('updated_at', 'desc')
        ->paginate(15);
        
    return view('admin.employees.inactive', [
        'inactiveEmployees' => $inactiveEmployees,
        'inactiveCount' => $inactiveEmployees->total()
    ]);
}

    public function restore($id)
    {
        $this->authorize('restore', User::class);
        
        $employee = User::where('status', 'inactive')->findOrFail($id);
        $employee->update(['status' => 'active']);
        
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'restore',
            'model_type' => User::class,
            'model_id' => $employee->id,
            'description' => "Reactivated user: {$employee->name}",
            'changes' => json_encode(['status' => 'active'])
        ]);
        
        return redirect()
            ->route('admin.employees.inactive')
            ->with([
                'success' => "{$employee->name} has been reactivated successfully",
                'inactiveUsers' => User::where('status', 'inactive')->count()
            ]);
    }

public function forceDelete($id)
{
    $user = auth()->user();
    $employee = User::where('status', 'inactive')->findOrFail($id);

    // فقط السوبر أدمن يمكنه الحذف الدائم
    if ($user->isSuperAdmin()) {
        $employee->forceDelete();
        return redirect()->route('admin.employees.inactive')->with('success', "User permanently deleted!");
    }

    return redirect()->back()->with('error', 'You are not authorized to permanently delete users!');
}

    public function deactivate(User $employee)
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $employee->update(['status' => 'inactive']);
            return redirect()->back()->with('success', 'User deactivated successfully');
        }
        
        if ($user->isAdmin()) {
            if ($employee->isSuperAdmin() || $employee->isAdmin()) {
                return redirect()->back()->with('error', 'You cannot deactivate this user');
            }
            $employee->update(['status' => 'inactive']);
            return redirect()->back()->with('success', 'User deactivated successfully');
        }
        
        if ($user->isDepartmentManager() && $employee->department_id === $user->department_id) {
            $employee->update(['status' => 'inactive']);
            return redirect()->back()->with('success', 'User deactivated successfully');
        }
        
        return redirect()->back()->with('error', 'You are not authorized to deactivate this user');
    }

public function activate(User $employee)
{
    $user = auth()->user();

    if ($user->isSuperAdmin() || 
        ($user->isAdmin() && !$employee->isSuperAdmin()) || 
        ($user->isDepartmentManager() && $employee->department_id === $user->department_id)) {
        
        $employee->update(['status' => 'active']);
        return redirect()->route('admin.employees.index')->with('success', "{$employee->name} activated successfully!");
    }

    return redirect()->back()->with('error', 'You are not authorized to activate this user!');
}

    
}