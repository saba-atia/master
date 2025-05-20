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
        $query = User::with('department')->active();
        
        if ($user->isDepartmentManager()) {
            $query->where('department_id', $user->department_id);
        }
        
        $employees = $query->get();
        $departments = Department::all();
        $inactiveCount = User::inactive()->count();
        
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
            'status' => 'active' // افتراضيًا يكون نشط
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
        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully');
    }


     public function destroy(User $employee)
    {
        $this->authorize('delete', $employee);
        
        $name = $employee->name;
        $employee->delete();
        
        return redirect()
            ->route('admin.employees.inactive')
            ->with('success', "Employee {$name} has been permanently deleted");
    }
    public function inactiveEmployees()
    {
        $this->authorize('viewAny', User::class);
        
        $employees = User::inactive()
            ->with(['department'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
            
        $lastDeactivated = User::inactive()
            ->latest('updated_at')
            ->value('updated_at');
            
        return view('admin.employees.inactive', [
            'employees' => $employees,
            'inactiveCount' => $employees->total(),
            'lastDeactivated' => $lastDeactivated ? $lastDeactivated->format('M d, Y') : null
        ]);
    }

    public function restore($id)
    {
        $this->authorize('restore', User::class);
        
        $employee = User::onlyTrashed()->findOrFail($id);
        $employee->restore();
        
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'restore',
            'model_type' => User::class,
            'model_id' => $employee->id,
            'description' => "Reactivated employee: {$employee->name}",
            'changes' => json_encode(['status' => 'active'])
        ]);
        
        return redirect()
            ->route('admin.employees.inactive')
            ->with([
                'success' => "{$employee->name} has been reactivated successfully",
                'inactiveEmployees' => User::onlyTrashed()->count()
            ]);
    }


        public function forceDelete($id)
    {
        $this->authorize('forceDelete', User::class);
        
        $employee = User::onlyTrashed()->findOrFail($id);
        $name = $employee->name;
        $employee->forceDelete();
        
        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'force_delete',
            'model_type' => User::class,
            'description' => "Permanently deleted employee: {$name}",
        ]);
        
        return redirect()
            ->route('admin.employees.inactive')
            ->with([
                'success' => "Employee {$name} has been permanently deleted",
                'inactiveEmployees' => User::onlyTrashed()->count()
            ]);
    }

 public function deactivate(User $employee)
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            $employee->update(['status' => 'inactive']);
            return redirect()->back()->with('success', 'Employee deactivated successfully');
        }
        
        if ($user->isAdmin()) {
            if ($employee->isSuperAdmin() || $employee->isAdmin()) {
                return redirect()->back()->with('error', 'You cannot deactivate this employee');
            }
            $employee->update(['status' => 'inactive']);
            return redirect()->back()->with('success', 'Employee deactivated successfully');
        }
        
        if ($user->isDepartmentManager() && $employee->department_id === $user->department_id) {
            $employee->update(['status' => 'inactive']);
            return redirect()->back()->with('success', 'Employee deactivated successfully');
        }
        
        return redirect()->back()->with('error', 'You are not authorized to deactivate this employee');
    }

    
 public function activate(User $employee)
    {
        $this->authorize('update', $employee);
        
        $employee->update(['status' => 'active']);
        
        return redirect()
            ->route('admin.employees.inactive')
            ->with('success', "{$employee->name} has been reactivated successfully");
    }

    
  
    
}