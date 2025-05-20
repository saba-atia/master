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

      $users = User::all();
    foreach($users as $user) {
        echo $user->profile_photo_path . "<br>";
    }
    $today = Carbon::today();
    
    $todayBirthdays = User::whereMonth('birth_date', $today->month)
                        ->whereDay('birth_date', $today->day)
                        ->with(['receivedWishes.sender'])
                        ->get();
    
    $endOfWeek = Carbon::today()->endOfWeek();
    
    $upcomingBirthdays = User::whereBetween('birth_date', [
            $today->addDay(),
            $endOfWeek
        ])
        ->orderByRaw("DATE_FORMAT(birth_date, '%m-%d')")
        ->get();

    return view('birthdays.index', [
        'todayBirthdays' => $todayBirthdays,
        'upcomingBirthdays' => $upcomingBirthdays
    ]);
}
    
public function sendWish(User $user, Request $request)
{
    $request->validate(['message' => 'required|string|max:500']);
    
    // تأكد من استخدام اسم الحقل الصحيح (receiver_id أو user_id)
    BirthdayWish::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $user->id, // أو 'user_id' => $user->id
        'message' => $request->message
    ]);
    
    return back()->with('success', 'تم إرسال التهنئة بنجاح!');
}
}