<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Department;
use App\Models\Branch;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
 public function show()
{
    $user = Auth::user()->load(['department']);
    
    // Ensure created_at has a value
    if (!$user->created_at) {
        $user->created_at = now();
    }
    
    return view('profile.show', compact('user'));
}

    public function edit()
    {
        $user = Auth::user();
        $departments = Department::all();

        return view('profile.edit', compact('user', 'departments'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'birth_date' => 'nullable|date|before_or_equal:today',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\+\-\(\)\s]+$/',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
        ], [
            'phone.regex' => 'The phone number format is invalid.',
            'photo.max' => 'The photo must not be greater than 2MB.',
            'birth_date.before_or_equal' => 'The birth date must be a date before or equal to today.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle photo removal
        if ($request->has('remove_photo') && $request->remove_photo) {
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
            }
            $data['photo_url'] = null;
        }

        // Handle new photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
            }

            // Store new photo
            $path = $request->file('photo')->store('avatars', 'public');
            $data['photo_url'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'password.min' => 'The password must be at least 8 characters.',
            'password.mixed' => 'The password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'The password must contain at least one number.',
            'password.symbols' => 'The password must contain at least one special character.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'The current password is incorrect'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }
}