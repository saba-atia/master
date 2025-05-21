@extends('dash.dash')

@section('title', 'Leave Requests')
@section('contentdash')

<link href="{{ asset('css/leave-vacation.css') }}" rel="stylesheet">

<!-- Toast Notification -->
@if(session('toast'))
<div class="toast show {{ session('toast')['type'] }}">
    <i class="fas fa-{{ session('toast')['type'] === 'success' ? 'check-circle' : 'exclamation-circle' }}"></i>
    <span>{{ session('toast')['message'] }}</span>
</div>
@endif

<!-- Leave Request Form -->
@if(!in_array(auth()->user()->role, ['super_admin']))
<div class="leave-vacation-form">
    <div class="form-header">
        <h2>Submit Leave Request</h2>
        <p>Please fill in the details of your leave request</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('leaves.store') }}">
        @csrf

        <div class="form-group">
            <label for="type" class="form-label">Leave Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="">Select Leave Type</option>
                <option value="personal" {{ old('type') == 'personal' ? 'selected' : '' }}>Personal Leave</option>
                <option value="official" {{ old('type') == 'official' ? 'selected' : '' }}>Official Leave</option>
            </select>
        </div>

        <div class="time-fields-container">
            <div class="form-group">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" name="start_time" id="start_time" class="form-control" 
                       value="{{ old('start_time') }}" required>
            </div>

            <div class="form-group">
                <label for="end_time" class="form-label">End Time</label>
                <input type="datetime-local" name="end_time" id="end_time" class="form-control" 
                       value="{{ old('end_time') }}" required>
            </div>
        </div>

        <div class="duration-display" id="duration-display">
            Duration: 0 hours
        </div>

        <div class="form-group">
            <label for="reason" class="form-label">Reason</label>
            <textarea name="reason" id="reason" class="form-control" rows="4" 
                      placeholder="Briefly explain the reason for your leave">{{ old('reason') }}</textarea>
        </div>

        <button type="submit" class="action-btn approve-btn" style="width: 100%; padding: 12px;">
            <i class="fas fa-paper-plane"></i> Submit Request
        </button>
    </form>
</div>
@endif

<!-- Filter Section for Admin/Super Admin -->
@if(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager']))
<div class="filter-container">
    <form method="GET" action="{{ route('leaves.index') }}" class="filter-form">
        <div class="form-group">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                @if(auth()->user()->role === 'department_manager')
                <option value="department_approved" {{ request('status') == 'department_approved' ? 'selected' : '' }}>Approved by Me</option>
                @endif
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-control">
                <option value="">All Types</option>
                <option value="personal" {{ request('type') == 'personal' ? 'selected' : '' }}>Personal</option>
                <option value="official" {{ request('type') == 'official' ? 'selected' : '' }}>Official</option>
            </select>
        </div>
        
        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
        <div class="form-group">
            <label for="user_id" class="form-label">Employee</label>
            <select name="user_id" id="user_id" class="form-control">
                <option value="">All Employees</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        
        <div class="form-group">
            <label for="start_date" class="form-label">From Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" 
                   value="{{ request('start_date') }}">
        </div>
        
        <div class="form-group">
            <label for="end_date" class="form-label">To Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" 
                   value="{{ request('end_date') }}">
        </div>
        
        <div class="form-group" style="display: flex; gap: 10px;">
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('leaves.index') }}" class="reset-btn">
                <i class="fas fa-sync-alt"></i> Reset
            </a>
        </div>
    </form>
</div>
@endif

<!-- Leaves List Section -->
<div class="leave-vacation-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; color: #3a3541; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-list"></i>
            @if(auth()->user()->role === 'department_manager')
                My Department Leave Requests
            @elseif(in_array(auth()->user()->role, ['admin', 'super_admin']))
                All Leave Requests
            @else
                My Leave Requests
            @endif
        </h3>
        
        <div style="color: #6c757d; font-size: 0.85rem;">
            Showing {{ $leaves->firstItem() }} - {{ $leaves->lastItem() }} of {{ $leaves->total() }} requests
        </div>
    </div>

    <div class="leave-vacation-table-container">
        @if(count($leaves) > 0)
        <table class="leave-vacation-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaves as $leave)
                <tr>
                    <td>
                        <div class="employee-cell">
                            @if($leave->user->photo_url && Storage::disk('public')->exists($leave->user->photo_url))
                                <img src="{{ asset('storage/'.$leave->user->photo_url) }}"
                                     alt="{{ $leave->user->name }}"
                                     class="employee-avatar">
                            @else
                                <div class="employee-avatar" style="background-color: {{ $leave->user->avatar_color }};">
                                    <span class="avatar-initials">{{ $leave->user->initials }}</span>
                                </div>
                            @endif
                            <div>
                                <div style="font-weight: 600;">{{ $leave->user->name }}</div>
                                <div style="color: #6c757d; font-size: 0.8rem;">{{ $leave->department->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="text-transform: capitalize;">{{ $leave->type }}</span>
                    </td>
                    <td>
                        <div style="font-weight: 500;">{{ \Carbon\Carbon::parse($leave->start_time)->format('M d, Y') }}</div>
                        <div style="color: #6c757d; font-size: 0.8rem;">{{ \Carbon\Carbon::parse($leave->start_time)->format('h:i A') }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 500;">{{ \Carbon\Carbon::parse($leave->end_time)->format('M d, Y') }}</div>
                        <div style="color: #6c757d; font-size: 0.8rem;">{{ \Carbon\Carbon::parse($leave->end_time)->format('h:i A') }}</div>
                    </td>
                    <td class="duration-cell">
                        {{ number_format($leave->duration_hours, 2) }} hrs
                    </td>
                    <td>
                        <span class="status-badge status-{{ $leave->status }}">
                            {{ ucfirst(str_replace('_', ' ', $leave->status)) }}
                        </span>
                       @if($leave->approved_by && $leave->approver)
    <div style="color: #6c757d; font-size: 0.75rem; margin-top: 3px;">
        by {{ $leave->approver->name }}
    </div>
@endif
                    </td>
                    <td>
                        <div class="actions-container">
                            @if(auth()->user()->role === 'department_manager' && 
                                $leave->status == 'pending' && 
                                $leave->user_id != auth()->id())
                                <!-- Department Manager Buttons -->
                                <form action="{{ route('leaves.update', $leave->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="department_approved">
                                    <button type="submit" class="action-btn approve-btn">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('leaves.update', $leave->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="action-btn reject-btn">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif

                            @if(auth()->user()->role === 'super_admin')
                                <!-- Super Admin Buttons -->
                                @if($leave->status === 'pending' || $leave->status === 'department_approved')
                                    <form action="{{ route('leaves.update', $leave->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="action-btn approve-btn">
                                            <i class="fas fa-check-circle"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('leaves.update', $leave->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="action-btn reject-btn">
                                            <i class="fas fa-times-circle"></i> Reject
                                        </button>
                                    </form>
                                @endif
                            @endif
                            
                            <!-- View Button -->
                            <button class="action-btn view-btn" onclick="showLeaveDetails({{ $leave->id }})">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h4>No leave requests found</h4>
            <p>@if(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager'])) No leave requests match your filters @else You haven't submitted any leave requests yet @endif</p>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($leaves->hasPages())
    <div style="display: flex; justify-content: center; margin-top: 1.5rem;">
        {{ $leaves->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Leave Details Modal -->
<div class="modal-overlay" id="leaveDetailsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 style="margin: 0;">
                <i class="fas fa-file-alt"></i>
                <span id="modalTitle">Leave Request Details</span>
            </h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
    // Calculate duration between start and end times
    function calculateDuration() {
        const startTime = document.getElementById('start_time');
        const endTime = document.getElementById('end_time');
        const durationDisplay = document.getElementById('duration-display');

        if (startTime.value && endTime.value) {
            const start = new Date(startTime.value);
            const end = new Date(endTime.value);
            const diffMs = end - start;

            if (diffMs < 0) {
                durationDisplay.textContent = 'Duration: 0 hours (End time must be after start time)';
                durationDisplay.style.color = '#dc3545';
                return;
            }

            const diffHours = diffMs / (1000 * 60 * 60);
            durationDisplay.textContent = `Duration: ${diffHours.toFixed(2)} hours`;
            durationDisplay.style.color = '#7367f0';
        } else {
            durationDisplay.textContent = 'Duration: 0 hours';
            durationDisplay.style.color = '#7367f0';
        }
    }

    // Show leave details in modal
    function showLeaveDetails(leaveId) {
        const modal = document.getElementById('leaveDetailsModal');
        const modalContent = document.getElementById('modalContent');
        
        // Show loading state
        modalContent.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #67c9f0;"></i>
                <p>Loading leave details...</p>
            </div>
        `;
        
        modal.classList.add('active');
        
        // Fetch leave details
        fetch(`/leaves/${leaveId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load leave details');
                }
                return response.json();
            })
            .then(data => {
                // Format dates
                const startDate = new Date(data.start_time).toLocaleString();
                const endDate = new Date(data.end_time).toLocaleString();
                const createdAt = new Date(data.created_at).toLocaleString();
                const updatedAt = new Date(data.updated_at).toLocaleString();
                
                // Update modal title
                document.getElementById('modalTitle').textContent = `Leave Request #${data.id}`;
                
                // Create modal content
                let content = `
                    <div class="detail-grid">
                        <div class="detail-section">
                            <h4><i class="fas fa-user"></i> Employee Information</h4>
                            <p><strong>Name:</strong> ${data.user.name}</p>
                            <p><strong>Department:</strong> ${data.department ? data.department.name : 'N/A'}</p>
                            <p><strong>Position:</strong> ${data.user.role}</p>
                        </div>
                        
                        <div class="detail-section">
                            <h4><i class="fas fa-calendar-check"></i> Request Details</h4>
                            <p><strong>Type:</strong> ${data.type.charAt(0).toUpperCase() + data.type.slice(1)}</p>
                            <p><strong>Status:</strong> <span class="status-${data.status}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span></p>
                            <p><strong>Duration:</strong> ${parseFloat(data.duration_hours).toFixed(2)} hours</p>
                        </div>
                        
                        <div class="detail-section">
                            <h4><i class="fas fa-calendar-day"></i> Dates</h4>
                            <p><strong>Start Time:</strong> ${startDate}</p>
                            <p><strong>End Time:</strong> ${endDate}</p>
                            <p><strong>Submitted:</strong> ${createdAt}</p>
                        </div>
                `;
                
                if (data.approver) {
                    content += `
                        <div class="detail-section">
                            <h4><i class="fas fa-user-check"></i> Approval</h4>
                            <p><strong>Approved By:</strong> ${data.approver.name}</p>
                            <p><strong>Decision Date:</strong> ${updatedAt}</p>
                            ${data.notes ? `<p><strong>Notes:</strong> ${data.notes}</p>` : ''}
                        </div>
                    `;
                }
                
                content += `
                    </div>
                    
                    <div class="detail-section">
                        <h4><i class="fas fa-file-alt"></i> Reason</h4>
                        <div class="reason-box">
                            ${data.reason || 'No reason provided'}
                        </div>
                    </div>
                `;
                
                modalContent.innerHTML = content;
            })
            .catch(error => {
                modalContent.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem;"></i>
                        <h3>Error Loading Details</h3>
                        <p>${error.message}</p>
                    </div>
                `;
            });
    }

    // Close modal
    function closeModal() {
        document.getElementById('leaveDetailsModal').classList.remove('active');
    }

    // Close modal when clicking outside
    document.getElementById('leaveDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Initialize event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        const startTime = document.getElementById('start_time');
        const endTime = document.getElementById('end_time');

        // Calculate duration when time inputs change
        if (startTime && endTime) {
            startTime.addEventListener('change', calculateDuration);
            endTime.addEventListener('change', calculateDuration);

            // Set minimum end time based on start time
            startTime.addEventListener('change', function() {
                if (this.value) {
                    endTime.min = this.value;
                }
            });

            // Calculate initial duration if values exist
            if (startTime.value || endTime.value) {
                calculateDuration();
            }
        }

        // Auto-hide toast after 5 seconds
        const toast = document.querySelector('.toast');
        if (toast) {
            setTimeout(() => {
                toast.classList.remove('show');
            }, 5000);
        }
    });
</script>
<style>
  /* ===== Base Styles ===== */
.leave-vacation-container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    line-height: 1.6;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* ===== Form Styles ===== */
.leave-vacation-form {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 30px;
}

.form-header {
    margin-bottom: 20px;
    text-align: center;
}

.form-header h2 {
    color: #2c3e50;
    margin-bottom: 5px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

.form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #67c9f0;
    box-shadow: 0 0 0 3px rgba(103, 201, 240, 0.2);
    outline: none;
}

.time-fields-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.duration-display {
    text-align: center;
    margin: 1rem 0;
    font-size: 0.9rem;
    color: #7367f0;
    font-weight: 500;
}

/* ===== Table Styles ===== */
.leave-vacation-table-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 30px;
}

.leave-vacation-table {
    width: 100%;
    border-collapse: collapse;
}

.leave-vacation-table th {
    background-color: #67c9f0;
    color: white;
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
}

.leave-vacation-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.leave-vacation-table tr:last-child td {
    border-bottom: none;
}

.leave-vacation-table tr:hover {
    background-color: #f9f9f9;
}

/* ===== Employee Cell ===== */
.employee-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.employee-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1rem;
}

.employee-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.avatar-initials {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    font-size: 1rem;
}

/* ===== Status Badges ===== */
.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-approved {
    background-color: #d4edda;
    color: #155724;
}

.status-rejected {
    background-color: #f8d7da;
    color: #721c24;
}

.status-department_approved {
    background-color: #d1ecf1;
    color: #0c5460;
}

/* ===== Action Buttons ===== */
.actions-container {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.action-btn {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: white;
}

.action-btn i {
    font-size: 0.8rem;
}

.approve-btn {
    background-color: #28a745;
}

.approve-btn:hover {
    background-color: #218838;
}

.reject-btn {
    background-color: #dc3545;
}

.reject-btn:hover {
    background-color: #c82333;
}

.view-btn {
    background-color: #17a2b8;
}

.view-btn:hover {
    background-color: #138496;
}

/* ===== Filter Section ===== */
.filter-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-btn {
    background-color: #67c9f0;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-btn:hover {
    background-color: #5ab0d5;
}

.reset-btn {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.reset-btn:hover {
    background-color: #5a6268;
}

/* ===== Empty State ===== */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    margin-top: 30px;
}

.empty-state i {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 15px;
}

/* ===== Modal Styles ===== */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: white;
    border-radius: 10px;
    width: 90%;
    max-width: 700px;
    max-height: 80vh;
    overflow-y: auto;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    transform: translateY(-20px);
    transition: all 0.3s;
}

.modal-overlay.active .modal-content {
    transform: translateY(0);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6c757d;
}

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.detail-section {
    margin-bottom: 20px;
}

.detail-section h4 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.reason-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #67c9f0;
}

/* ===== Toast Notification ===== */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 8px;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1100;
    display: flex;
    align-items: center;
    gap: 10px;
    transform: translateX(150%);
    transition: transform 0.3s ease;
}

.toast.show {
    transform: translateX(0);
}

.toast.success {
    background-color: #28a745;
}

.toast.error {
    background-color: #dc3545;
}

.toast i {
    font-size: 1.2rem;
}

/* ===== Duration Cell ===== */
.duration-cell {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #495057;
}

.duration-cell:hover {
    color: #67c9f0;
}

/* ===== Responsive Adjustments ===== */
@media (max-width: 768px) {
    .filter-form {
        grid-template-columns: 1fr;
    }
    
    .leave-vacation-table-container {
        overflow-x: auto;
    }
    
    .actions-container {
        flex-direction: column;
        gap: 5px;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
    
    .time-fields-container {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection