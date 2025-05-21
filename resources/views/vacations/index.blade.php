@extends('dash.dash')

@section('title', 'Vacation Requests')
@section('contentdash')

<link href="{{ asset('css/leave-vacation.css') }}" rel="stylesheet">

<!-- Toast Notification -->
@if(session('toast'))
<div class="toast show {{ session('toast')['type'] }}">
    <i class="fas fa-{{ session('toast')['type'] === 'success' ? 'check-circle' : 'exclamation-circle' }}"></i>
    <span>{{ session('toast')['message'] }}</span>
</div>
@endif

<!-- Vacation Request Form -->
@if(!in_array(auth()->user()->role, ['super_admin']))
<div class="leave-vacation-form">
    <div class="form-header">
        <h2>Submit Vacation Request</h2>
        <p>Please fill in your vacation details</p>
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

    <form method="POST" action="{{ route('vacations.store') }}">
        @csrf

        <div class="form-group">
            <label for="type" class="form-label">Vacation Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="">Select Type</option>
                <option value="annual">Annual Leave</option>
                <option value="sick">Sick Leave</option>
                <option value="unpaid">Unpaid Leave</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="reason" class="form-label">Reason</label>
            <textarea name="reason" id="reason" class="form-control" rows="4" placeholder="Enter reason for your vacation"></textarea>
        </div>

        <button type="submit" class="action-btn approve-btn" style="width: 100%; padding: 12px;">
            <i class="fas fa-paper-plane"></i> Submit Request
        </button>
    </form>
</div>
@endif

<!-- Filter Section -->
@if(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager']))
<div class="filter-container">
    <form method="GET" action="{{ route('vacations.index') }}" class="filter-form">
        <div class="form-group">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-control">
                <option value="">All Types</option>
                <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Annual</option>
                <option value="sick" {{ request('type') == 'sick' ? 'selected' : '' }}>Sick</option>
                <option value="unpaid" {{ request('type') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
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
            <input type="date" name="start_date" id="filter_start_date" class="form-control" 
                   value="{{ request('start_date') }}">
        </div>
        
        <div class="form-group">
            <label for="end_date" class="form-label">To Date</label>
            <input type="date" name="end_date" id="filter_end_date" class="form-control" 
                   value="{{ request('end_date') }}">
        </div>
        
        <div class="form-group" style="display: flex; gap: 10px;">
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('vacations.index') }}" class="reset-btn">
                <i class="fas fa-sync-alt"></i> Reset
            </a>
        </div>
    </form>
</div>
@endif

<!-- Vacation Requests Table -->
<div class="leave-vacation-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-list"></i>
            @if(auth()->user()->role === 'department_manager')
                Department Vacation Requests
            @elseif(in_array(auth()->user()->role, ['admin', 'super_admin']))
                All Vacation Requests
            @else
                My Vacation Requests
            @endif
        </h3>
        <div style="color: #6c757d;">
            Showing {{ $vacations->firstItem() }} - {{ $vacations->lastItem() }} of {{ $vacations->total() }}
        </div>
    </div>

    <div class="leave-vacation-table-container">
        @if(count($vacations) > 0)
        <table class="leave-vacation-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vacations as $vacation)
                <tr>
                   <td>
    <div class="employee-cell">
        @if($vacation->user)
            @if($vacation->user->photo_url && Storage::disk('public')->exists($vacation->user->photo_url))
                <img src="{{ asset('storage/'.$vacation->user->photo_url) }}"
                     alt="{{ $vacation->user->name }}"
                     class="employee-avatar">
            @else
                <div class="employee-avatar" style="background-color: {{ $vacation->user->avatar_color ?? '#6c757d' }};">
                    <span class="avatar-initials">{{ $vacation->user->initials ?? '??' }}</span>
                </div>
            @endif
            <div>
                <div style="font-weight: 600;">{{ $vacation->user->name ?? 'Deleted User' }}</div>
                <div style="color: #6c757d; font-size: 0.8rem;">{{ $vacation->department->name ?? 'N/A' }}</div>
            </div>
        @else
            <div class="employee-avatar" style="background-color: #6c757d;">
                <span class="avatar-initials">??</span>
            </div>
            <div>
                <div style="font-weight: 600;">Deleted User</div>
                <div style="color: #6c757d; font-size: 0.8rem;">N/A</div>
            </div>
        @endif
    </div>
</td>
                    <td>{{ ucfirst($vacation->type) }}</td>
                    <td>{{ \Carbon\Carbon::parse($vacation->start_date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($vacation->end_date)->format('M d, Y') }}</td>
                    <td>{{ $vacation->days_taken }}</td>
                   <td>
    <span class="status-badge status-{{ $vacation->status }}">
        {{ ucfirst($vacation->status) }}
    </span>
    
    @if($vacation->approved_by)
        <div style="color: #6c757d; font-size: 0.75rem; margin-top: 3px;">
            by {{ $vacation->approver?->name ?? 'Unknown Approver' }}
        </div>
    @endif
</td>
                    <td>
                        <div class="actions-container">
                            <!-- Department Manager Actions -->
                            @if(auth()->user()->role === 'department_manager' && 
                                $vacation->user->department_id === auth()->user()->department_id &&
                                $vacation->status === 'pending')
                                <form action="{{ route('vacations.update', $vacation->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="action-btn approve-btn">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('vacations.update', $vacation->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="action-btn reject-btn">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif

                            <!-- Admin Actions -->
                            @if(auth()->user()->role === 'admin' && 
                                $vacation->status === 'pending' &&
                                $vacation->user->role !== 'admin')
                                <form action="{{ route('vacations.update', $vacation->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="action-btn approve-btn">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('vacations.update', $vacation->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="action-btn reject-btn">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif

                            <!-- Super Admin Actions -->
                            @if(auth()->user()->role === 'super_admin' && 
                                $vacation->status === 'pending')
                                <form action="{{ route('vacations.update', $vacation->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="action-btn approve-btn">
                                        <i class="fas fa-check-double"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('vacations.update', $vacation->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="action-btn reject-btn">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            @endif

                            <!-- View Button for All -->
                            <button class="action-btn view-btn" onclick="showVacationDetails({{ $vacation->id }})">
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
            <h3>No vacation requests found</h3>
            <p>
                @if(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager']))
                    No requests match your current filters
                @else
                    You haven't submitted any vacation requests yet
                @endif
            </p>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($vacations->hasPages())
    <div style="display: flex; justify-content: center; margin-top: 20px;">
        {{ $vacations->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Vacation Details Modal -->
<div class="modal-overlay" id="vacationDetailsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 style="margin: 0;">
                <i class="fas fa-file-alt"></i>
                <span id="modalTitle">Vacation Request Details</span>
            </h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div id="modalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
    // Show vacation details modal
    function showVacationDetails(vacationId) {
        const modal = document.getElementById('vacationDetailsModal');
        const modalContent = document.getElementById('modalContent');
        
        // Show loading state
        modalContent.innerHTML = `
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #67c9f0;"></i>
                <p>Loading vacation details...</p>
            </div>
        `;
        
        modal.classList.add('active');
        
        // Fetch vacation details
        fetch(`/vacations/${vacationId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to load vacation details');
                }
                return response.json();
            })
            .then(data => {
                // Format dates
                const startDate = new Date(data.start_date).toLocaleDateString();
                const endDate = new Date(data.end_date).toLocaleDateString();
                const createdAt = new Date(data.created_at).toLocaleString();
                const updatedAt = new Date(data.updated_at).toLocaleString();
                
                // Update modal title
                document.getElementById('modalTitle').textContent = `Vacation Request #${data.id}`;
                
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
                            <p><strong>Days:</strong> ${data.days_taken}</p>
                        </div>
                        
                        <div class="detail-section">
                            <h4><i class="fas fa-calendar-day"></i> Dates</h4>
                            <p><strong>Start Date:</strong> ${startDate}</p>
                            <p><strong>End Date:</strong> ${endDate}</p>
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
        document.getElementById('vacationDetailsModal').classList.remove('active');
    }

    // Close modal when clicking outside
    document.getElementById('vacationDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Initialize event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide toast after 5 seconds
        const toast = document.querySelector('.toast');
        if (toast) {
            setTimeout(() => {
                toast.classList.remove('show');
            }, 5000);
        }
        
        // Set minimum end date based on start date
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                if (this.value) {
                    endDateInput.min = this.value;
                }
            });
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