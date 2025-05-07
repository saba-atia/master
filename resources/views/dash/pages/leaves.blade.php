@extends('dash.dash')
@section('title', 'Leave Request')
@section('contentdash')

<style>
    .leave-form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .form-header {
        margin-bottom: 2rem;
        text-align: center;
    }

    .form-header h2 {
        color: #3a3541;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .form-header p {
        color: #6d6d6d;
        font-size: 0.95rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #3a3541;
        font-size: 0.875rem;
    }

    .form-select, .form-input, .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-select:focus, .form-input:focus, .form-textarea:focus {
        border-color: #7367f0;
        box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.1);
        outline: none;
    }

    .time-fields-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .submit-btn {
        background-color: #7367f0;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 1rem;
    }

    .submit-btn:hover {
        background-color: #5e50d8;
        transform: translateY(-2px);
    }

    .duration-display {
        text-align: center;
        margin: 1rem 0;
        font-size: 0.9rem;
        color: #7367f0;
        font-weight: 500;
    }

    .note {
        font-size: 0.85rem;
        color: #ff0000;
        margin-top: 1rem;
        text-align: center;
    }

    /* Toast Notifications */
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        display: flex;
        align-items: center;
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
    
    .toast.warning {
        background-color: #ffc107;
    }
    
    .toast i {
        margin-right: 10px;
    }

    /* Leaves Table Styles */
    .leaves-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2rem;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .leaves-table th, .leaves-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .leaves-table th {
        background-color: #7367f0;
        color: white;
        font-weight: 600;
    }
    
    .leaves-table tr:hover {
        background-color: #f9f9f9;
    }
    
    /* Status Badges */
    .status-pending {
        color: #ffc107;
        font-weight: 600;
    }
    
    .status-approved {
        color: #28a745;
        font-weight: 600;
    }
    
    .status-rejected {
        color: #dc3545;
        font-weight: 600;
    }
    
    .status-department_approved {
        color: #17a2b8;
        font-weight: 600;
    }
    
    /* Action Buttons */
    .action-btn {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.85rem;
        margin-right: 5px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .approve-btn {
        background-color: #28a745;
        color: white;
    }
    
    .approve-btn:hover {
        background-color: #218838;
    }
    
    .reject-btn {
        background-color: #dc3545;
        color: white;
    }
    
    .reject-btn:hover {
        background-color: #c82333;
    }
    
    .view-btn {
        background-color: #17a2b8;
        color: white;
    }
    
    .view-btn:hover {
        background-color: #138496;
    }
    
    .actions-cell {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    /* Empty State */
    .no-leaves {
        text-align: center;
        padding: 2rem;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-top: 2rem;
    }
    
    /* Modal Styles */
    #leaveDetailsModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1001;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    .modal-close-btn {
        margin-top: 1.5rem;
        padding: 0.5rem 1rem;
        background: #7367f0;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .modal-close-btn:hover {
        background: #5e50d8;
    }
    
    /* Filter Styles */
    .filter-container {
        background: #fff;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    
    .filter-form {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }
    
    .filter-group {
        margin-bottom: 0;
    }
    
    .filter-btn {
        background-color: #7367f0;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .filter-btn:hover {
        background-color: #5e50d8;
    }
    
    .reset-btn {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .reset-btn:hover {
        background-color: #5a6268;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .time-fields-container {
            grid-template-columns: 1fr;
        }
        
        .leaves-table {
            display: block;
            overflow-x: auto;
        }
        
        .actions-cell {
            flex-direction: column;
            gap: 5px;
        }
        
        .action-btn {
            width: 100%;
            justify-content: center;
        }
        
        .filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Toast Notification -->
@if(session('toast'))
<div class="toast {{ session('toast')['type'] }} show">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('toast')['message'] }}</span>
</div>

<script>
    setTimeout(() => {
        document.querySelector('.toast').classList.remove('show');
    }, 5000);
</script>
@endif

<!-- Leave Request Form -->
@if(!in_array(auth()->user()->role, ['super_admin']))
<div class="leave-form-container">
    <div class="form-header">
        <h2>Submit Leave Request</h2>
        <p>Please fill in the details of your leave request</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="color: #dc3545; margin-bottom: 1rem; padding: 0.75rem 1.25rem; border-radius: 0.25rem; background-color: #f8d7da; border: 1px solid #f5c6cb;">
            <ul style="list-style: none; padding: 0; margin: 0;">
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
            <select name="type" id="type" class="form-select" required>
                <option value="">Select Leave Type</option>
                <option value="personal" {{ old('type') == 'personal' ? 'selected' : '' }}>Personal Leave</option>
                <option value="official" {{ old('type') == 'official' ? 'selected' : '' }}>Official Leave</option>
            </select>
        </div>

        <div class="time-fields-container">
            <div class="form-group">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" name="start_time" id="start_time" class="form-input" 
                       value="{{ old('start_time') }}" required>
            </div>

            <div class="form-group">
                <label for="end_time" class="form-label">End Time</label>
                <input type="datetime-local" name="end_time" id="end_time" class="form-input" 
                       value="{{ old('end_time') }}" required>
            </div>
        </div>

        <div class="duration-display" id="duration-display">
            Duration: 0 hours
        </div>

        <div class="form-group">
            <label for="reason" class="form-label">Reason</label>
            <textarea name="reason" id="reason" class="form-textarea" rows="4" 
                      placeholder="Briefly explain the reason for your leave">{{ old('reason') }}</textarea>
        </div>

        <div class="note">
            <p><strong>Note:</strong> Any leave request that exceeds 2 hours will require additional approval.</p>
        </div>

        <button type="submit" class="submit-btn">
            <i class="fas fa-paper-plane"></i> Submit Request
        </button>
    </form>
</div>
@endif

<!-- Filter Section for Admin/Super Admin -->
@if(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager']))
<div class="filter-container">
    <form method="GET" action="{{ route('leaves.index') }}" class="filter-form">
        <div class="form-group filter-group">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                @if(auth()->user()->role === 'department_manager')
                <option value="department_approved" {{ request('status') == 'department_approved' ? 'selected' : '' }}>Approved by Me</option>
                @endif
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        
        <div class="form-group filter-group">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select">
                <option value="">All Types</option>
                <option value="personal" {{ request('type') == 'personal' ? 'selected' : '' }}>Personal</option>
                <option value="official" {{ request('type') == 'official' ? 'selected' : '' }}>Official</option>
            </select>
        </div>
        
        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
        <div class="form-group filter-group">
            <label for="user_id" class="form-label">Employee</label>
            <select name="user_id" id="user_id" class="form-select">
                <option value="">All Employees</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        
        <div class="form-group filter-group">
            <label for="start_date" class="form-label">From Date</label>
            <input type="date" name="start_date" id="start_date" class="form-input" 
                   value="{{ request('start_date') }}">
        </div>
        
        <div class="form-group filter-group">
            <label for="end_date" class="form-label">To Date</label>
            <input type="date" name="end_date" id="end_date" class="form-input" 
                   value="{{ request('end_date') }}">
        </div>
        
        <div class="form-group filter-group" style="display: flex; gap: 10px;">
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
@if(count($leaves) > 0)
<div class="leaves-list-container">
    <h3 style="margin-bottom: 1.5rem; color: #3a3541; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-list"></i>
        @if(auth()->user()->role === 'department_manager')
            My Department Leave Requests
        @elseif(in_array(auth()->user()->role, ['admin', 'super_admin']))
            All Leave Requests
        @else
            My Leave Requests
        @endif
    </h3>
    
    <div style="overflow-x: auto;">
        <table class="leaves-table">
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
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if($leave->user->avatar)
                                <img src="{{ asset('storage/avatars/' . $leave->user->avatar) }}" 
                                     alt="{{ $leave->user->name }}" 
                                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #7367f0; color: white; display: flex; align-items: center; justify-content: center;">
                                    {{ substr($leave->user->name, 0, 1) }}
                                </div>
                            @endif
                            <span>{{ $leave->user->name }}</span>
                        </div>
                    </td>
                    <td>{{ ucfirst($leave->type) }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave->start_time)->format('M d, Y H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave->end_time)->format('M d, Y H:i') }}</td>
                    <td>{{ number_format($leave->duration_hours, 2) }} hours</td>
                    <td class="status-{{ $leave->status }}">
                        {{ ucfirst(str_replace('_', ' ', $leave->status)) }}
                        @if($leave->approved_by)
                            <br><small style="font-size: 0.75rem; color: #6c757d;">by {{ $leave->approver->name }}</small>
                        @endif
                    </td>
                    <td class="actions-cell">
                        @if(auth()->user()->role === 'department_manager' && $leave->status == 'pending')
                        <form action="{{ route('leaves.update', $leave->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="action-btn approve-btn" title="Approve">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <form action="{{ route('leaves.update', $leave->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="action-btn reject-btn" title="Reject">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                        @endif
                        
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']) && $leave->status == 'department_approved')
                        <form action="{{ route('leaves.update', $leave->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="action-btn approve-btn" title="Final Approve">
                                <i class="fas fa-check-double"></i> Final Approve
                            </button>
                        </form>
                        <form action="{{ route('leaves.update', $leave->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="action-btn reject-btn" title="Reject">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                        @endif
                        
                        <button class="action-btn view-btn" title="View Details" onclick="showLeaveDetails({{ $leave->id }})">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($leaves->hasPages())
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $leaves->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@else
<div class="no-leaves">
    <i class="fas fa-inbox" style="font-size: 2rem; color: #6c757d; margin-bottom: 1rem;"></i>
    <h4>No leave requests found</h4>
    <p>@if(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager'])) No leave requests match your filters @else You haven't submitted any leave requests yet @endif</p>
</div>
@endif

<!-- Leave Details Modal -->
<div id="leaveDetailsModal">
    <div class="modal-content">
        <h3 id="modalTitle" style="margin-bottom: 1.5rem; color: #3a3541; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-file-alt"></i>
            <span>Leave Request Details</span>
        </h3>
        <div id="modalContent" style="line-height: 1.6;">
            <!-- Content will be loaded dynamically -->
        </div>
        <button onclick="closeModal()" class="modal-close-btn">
            <i class="fas fa-times"></i> Close
        </button>
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
        fetch(`/leaves/${leaveId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('modalTitle').innerHTML = `
                    <i class="fas fa-file-alt"></i>
                    <span>Leave Request #${data.id}</span>
                `;
                
                let content = `
                    <div style="margin-bottom: 1rem;">
                        <p><strong style="min-width: 120px; display: inline-block;">Employee:</strong> ${data.user.name}</p>
                        <p><strong style="min-width: 120px; display: inline-block;">Department:</strong> ${data.department ? data.department.name : 'N/A'}</p>
                        <p><strong style="min-width: 120px; display: inline-block;">Role:</strong> ${data.user.role.replace('_', ' ').charAt(0).toUpperCase() + data.user.role.replace('_', ' ').slice(1)}</p>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <p><strong style="min-width: 120px; display: inline-block;">Type:</strong> 
                            <span class="badge" style="background: #e9ecef; color: #495057; padding: 0.25em 0.4em; border-radius: 0.25rem;">
                                ${data.type.charAt(0).toUpperCase() + data.type.slice(1)}
                            </span>
                        </p>
                        <p><strong style="min-width: 120px; display: inline-block;">Status:</strong> 
                            <span class="status-${data.status}" style="padding: 0.25em 0.4em; border-radius: 0.25rem;">
                                ${data.status.replace('_', ' ').charAt(0).toUpperCase() + data.status.replace('_', ' ').slice(1)}
                            </span>
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <p><strong style="min-width: 120px; display: inline-block;">Start Time:</strong> ${new Date(data.start_time).toLocaleString()}</p>
                        <p><strong style="min-width: 120px; display: inline-block;">End Time:</strong> ${new Date(data.end_time).toLocaleString()}</p>
                        <p><strong style="min-width: 120px; display: inline-block;">Duration:</strong> ${parseFloat(data.duration_hours).toFixed(2)} hours</p>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <p><strong style="min-width: 120px; display: inline-block;">Reason:</strong></p>
                        <div style="background: #f8f9fa; padding: 0.75rem; border-radius: 0.25rem; border: 1px solid #e9ecef;">
                            ${data.reason || 'No reason provided'}
                        </div>
                    </div>
                `;
                
                if (data.approved_by) {
                    content += `
                        <div style="margin-bottom: 1rem;">
                            <p><strong style="min-width: 120px; display: inline-block;">Approved By:</strong> ${data.approver.name}</p>
                            <p><strong style="min-width: 120px; display: inline-block;">Approval Date:</strong> ${new Date(data.updated_at).toLocaleString()}</p>
                        </div>
                    `;
                }
                
                if (data.notes) {
                    content += `
                        <div style="margin-bottom: 1rem;">
                            <p><strong style="min-width: 120px; display: inline-block;">Approver Notes:</strong></p>
                            <div style="background: #f8f9fa; padding: 0.75rem; border-radius: 0.25rem; border: 1px solid #e9ecef;">
                                ${data.notes}
                            </div>
                        </div>
                    `;
                }
                
                document.getElementById('modalContent').innerHTML = content;
                document.getElementById('leaveDetailsModal').style.display = 'flex';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Failed to load leave request details. Please try again.
                    </div>
                `;
                document.getElementById('leaveDetailsModal').style.display = 'flex';
            });
    }

    // Close modal
    function closeModal() {
        document.getElementById('leaveDetailsModal').style.display = 'none';
    }

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
    });
</script>

@endsection