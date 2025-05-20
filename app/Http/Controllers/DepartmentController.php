<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function employees()
    {
        $user = Auth::user();
        $employees = User::where('department_id', $user->department_id)
                       ->with('attendances')
                       ->get();
        
        return view('department.employees', compact('employees'));
    }
}