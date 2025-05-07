<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Vacation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VacationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Vacation::with(['user', 'approver', 'department']);
    
        // Apply status filter
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }
        
        // Apply type filter
        if (request()->has('type') && request('type') != '') {
            $query->where('type', request('type'));
        }
        
        // Apply user filter for admins and super admins
        if (request()->has('user_id') && request('user_id') != '' && 
            in_array($user->role, ['admin', 'super_admin'])) {
            $query->where('user_id', request('user_id'));
        }
        
        // Apply date filters
        if (request()->has('start_date') && request('start_date') != '') {
            $query->whereDate('start_date', '>=', request('start_date'));
        }
        
        if (request()->has('end_date') && request('end_date') != '') {
            $query->whereDate('end_date', '<=', request('end_date'));
        }
    
        // Apply role-based filtering
        if ($user->role === 'department_manager') {
            // Department managers see only their department's requests (excluding their own)
            $query->whereHas('user', function($q) use ($user) {
                $q->where('department_id', $user->department_id)
                  ->where('id', '!=', $user->id);
            });
        } elseif (!in_array($user->role, ['admin', 'super_admin'])) {
            // Regular employees see only their own requests
            $query->where('user_id', $user->id);
        }
    
        $vacations = $query->latest()->paginate(10);
    
        // Get users for filter dropdown
        $users = $this->getFilterableUsers($user);
    
        return view('vacations.index', [
            'vacations' => $vacations,
            'users' => $users,
            'message' => session('message')
        ]);
    }

    private function getFilterableUsers($user)
    {
        if (!in_array($user->role, ['admin', 'super_admin', 'department_manager'])) {
            return [];
        }

        $usersQuery = User::query();
        
        if ($user->role === 'department_manager') {
            $usersQuery->where('department_id', $user->department_id)
                      ->where('id', '!=', $user->id);
        } elseif ($user->role === 'admin') {
            $usersQuery->whereIn('role', ['department_manager', 'admin']);
        }
        
        return $usersQuery->get(['id', 'name']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:annual,sick,unpaid,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $user = Auth::user();

            if (!$user->department_id) {
                return back()->withErrors(['error' => 'You must be assigned to a department to submit vacation requests']);
            }

            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);
            
            // Calculate working days (excluding weekends)
            $days = $start->diffInDaysFiltered(function(Carbon $date) {
                return !$date->isWeekend();
            }, $end) + 1; // Include the start date

            Vacation::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'type' => $validated['type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'days_taken' => $days,
                'reason' => $validated['reason'] ?? null,
                'status' => 'pending'
            ]);

            return redirect()->route('vacations.index')
                ->with('success', 'Vacation request submitted successfully.')
                ->with('toast', [
                    'type' => 'success',
                    'message' => 'Your vacation request has been submitted and is pending approval.'
                ]);

        } catch (\Exception $e) {
            return back()->withInput()
                        ->withErrors(['error' => 'Failed to submit vacation request: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $vacation = Vacation::with('user')->findOrFail($id);
        $user = Auth::user();

        $this->authorizeAction($vacation, $user);

        $validStatuses = $this->getValidStatuses($user);
        $validated = $request->validate([
            'status' => ['required', Rule::in($validStatuses)],
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $vacation->update([
                'status' => $validated['status'],
                'approved_by' => $user->id,
                'notes' => $validated['notes'] ?? null
            ]);

            return back()->with('success', 'Vacation request updated successfully.')
                        ->with('toast', [
                            'type' => 'success',
                            'message' => 'Vacation status updated successfully'
                        ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update vacation: ' . $e->getMessage());
        }
    }

    private function authorizeAction($vacation, $user)
    {
        switch ($user->role) {
            case 'super_admin':
                // Super admins can only approve admin requests
                if ($vacation->user->role !== 'admin') {
                    abort(403, 'Not authorized to approve this vacation request');
                }
                return true;

            case 'admin':
                // Admins can only approve department_manager requests
                if ($vacation->user->role !== 'department_manager') {
                    abort(403, 'Not authorized to approve this vacation request');
                }
                return true;

            case 'department_manager':
                // Department managers only on their own department's employees (excluding themselves and other managers)
                if ($vacation->user->department_id === $user->department_id &&
                $vacation->user->id !== $user->id &&
                $vacation->user->role !== 'department_manager') {
                return true;
            }
            abort(403, 'Not authorized to approve this vacation request');

            default:
                abort(403, 'Not authorized to approve vacations');
        }
    }

    private function getValidStatuses($user)
    {
        if (in_array($user->role, ['super_admin', 'admin', 'department_manager'])) {
            return ['approved', 'rejected'];
        }
        return [];
    }

    public function show($id)
    {
        $vacation = Vacation::with(['user', 'approver', 'department'])->findOrFail($id);
        return response()->json($vacation);
    }
}
