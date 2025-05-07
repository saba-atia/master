<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Wish;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BirthdayController extends Controller
{
    public function index()
    {
        $today = now()->format('m-d');
    
        // Employees with birthdays today
        $todayBirthdays = User::whereNotNull('birth_date')
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') = ?", [$today])
            ->get();
    
        // All departments
        $departments = Department::all();
    
        // Employee counts
        $totalEmployees = User::count();
        $birthdaysToday = $todayBirthdays->count();
        $birthdaysThisMonth = User::whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->count();
        $birthdaysNextMonth = User::whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->addMonth()->month)
            ->count();
    
        // Employees without birthdays
        $employeesWithoutBirthdays = User::whereNull('birth_date')->get();
    
        // Upcoming birthdays
        $upcomingBirthdays = User::whereNotNull('birth_date')
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') > ?", [now()->format('m-d')])
            ->orderByRaw("DATE_FORMAT(birth_date, '%m-%d')")
            ->limit(10)
            ->get();
    
        return view('birthdays.index', [
            'todayBirthdays' => $todayBirthdays,
            'departments' => $departments,
            'totalEmployees' => $totalEmployees,
            'birthdaysToday' => $birthdaysToday,
            'birthdaysThisMonth' => $birthdaysThisMonth,
            'birthdaysNextMonth' => $birthdaysNextMonth,
            'employeesWithoutBirthdays' => $employeesWithoutBirthdays,
            'upcomingBirthdays' => $upcomingBirthdays,
        ]);
    }
    
    public function myBirthday()
    {
        $user = auth()->user();
        $birthdayWishes = Wish::where('recipient_id', $user->id)
                           ->with('sender')
                           ->latest()
                           ->get();

        return view('birthdays.my_birthday', [
            'user' => $user,
            'wishes' => $birthdayWishes
        ]);
    }

    public function getBirthdaysByDate($date)
    {
        $user = auth()->user();
        $date = Carbon::parse($date);
        $monthDay = $date->format('m-d');
        
        $query = User::whereRaw("DATE_FORMAT(birth_date, '%m-%d') = ?", [$monthDay]);
        
        if ($user->role === 'department_head') {
            $query->where('department_id', $user->department_id);
        }
        
        if (in_array($user->role, ['admin', 'super_admin']) && request()->has('department_id')) {
            $departmentId = request('department_id');
            if ($departmentId !== 'all') {
                $query->where('department_id', $departmentId);
            }
        }
        
        $birthdays = $query->with('department')->get();
        
        return response()->json($birthdays);
    }

    public function sendWishes(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'message' => 'required|string|max:500'
        ]);
        
        $wish = Wish::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $request->employee_id,
            'message' => $request->message
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Birthday wishes sent successfully!',
            'wish' => $wish
        ]);
    }
}