<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BirthdayWish;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BirthdayController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $endOfWeek = $today->copy()->endOfWeek();
        
        // Query for birthdays this week (including today)
        $users = User::whereNotNull('birth_date')
            ->where(function($query) use ($today, $endOfWeek) {
                // Birthdays in this week's month
                $query->whereMonth('birth_date', $today->month)
                      ->whereDay('birth_date', '>=', $today->day)
                      ->whereDay('birth_date', '<=', $endOfWeek->day);
            })
            ->orWhere(function($query) use ($today, $endOfWeek) {
                // Special case if the week spans two months (e.g., last week of December)
                if ($today->month != $endOfWeek->month) {
                    $query->whereMonth('birth_date', $endOfWeek->month)
                          ->whereDay('birth_date', '<=', $endOfWeek->day);
                }
            })
            ->with(['birthdayWishes' => function($query) {
                $query->latest()->with('sender');
            }])
            ->orderByRaw('MONTH(birth_date), DAY(birth_date)')
            ->get();
    
        // Separate today's birthdays from the rest
        $todayBirthdays = $users->filter(function($user) use ($today) {
            return $user->birth_date->isBirthday();
        });
    
        $suggestedMessages = [
            "Happy birthday! 🎉",
            "Wishing you all the best! 🎂",
            "Many happy returns! 🥳",
            "Have a wonderful birthday! 🎁",
            "Warmest wishes on your birthday! 🎊"
        ];
    
        return view('birthdays.index', [
            'users' => $users,
            'todayBirthdays' => $todayBirthdays,
            'suggestedMessages' => $suggestedMessages
        ]);
    }
    
    public function sendWish(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:255'
        ]);
        
        BirthdayWish::create([
            'user_id' => $user->id,
            'sender_id' => auth()->id(),
            'message' => $request->message
        ]);
        
        return back()->with('success', 'Birthday wish sent successfully!');
    }
}