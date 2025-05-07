<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Calculate birthday information
        $upcomingBirthday = null;
        if ($user->birth_date) {
            $birthDate = Carbon::parse($user->birth_date);
            $today = Carbon::today();
            $nextBirthday = Carbon::create($today->year, $birthDate->month, $birthDate->day);
            
            if ($today->gt($nextBirthday)) {
                $nextBirthday->addYear();
            }
            
            $daysRemaining = $today->diffInDays($nextBirthday);
            $age = $nextBirthday->year - $birthDate->year;
            
            $upcomingBirthday = [
                'days_remaining' => $daysRemaining,
                'age' => $age
            ];
        }
        
        return view('profile.show', compact('user', 'upcomingBirthday'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if (!str_starts_with($value->getMimeType(), 'image/')) {
                        $fail('The file must be an image.');
                    }
                }
            ],
        ], [
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => 'Allowed formats: jpeg, png, jpg, gif, webp',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->bio = $validated['bio'];
        $user->birth_date = $validated['birth_date'];

        if ($request->hasFile('avatar')) {
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->photo_url = $path;
        }

        $user->save();

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.')
            ->with('birth_date', $request->birth_date);
    }
}