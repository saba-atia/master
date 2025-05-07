@extends('dash.dash')
@section('title', 'Vacation Requests')
@section('contentdash')

<style>
    .vacation-form-container {
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

    .date-fields-container {
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

    /* Vacations Table Styles */
    .vacations-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2rem;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .vacations-table th, .vacations-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .vacations-table th {
        background-color: #7367f0;
        color: white;
        font-weight: 600;
    }
    
    .vacations-table tr:hover {
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
    .no-vacations {
        text-align: center;
        padding: 2rem;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-top: 2rem;
    }
    
    /* Modal Styles */
    #vacationDetailsModal {
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
        .date-fields-container {
            grid-template-columns: 1fr;
        }
        
        .vacations-table {
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

<!-- Vacation Request Form -->
@if(!in_array(auth()->user()->role, ['super_admin']))
<div class="vacation-form-container">
    <div class="form-header">
        <h2>Submit Vacation Request</h2>
        <p>Please fill in the details of your vacation request</p>
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

    <form method="POST" action="{{ route('vacations.store') }}">
        @csrf

        <div class="form-group">
            <label for="type" class="form-label">Vacation Type</label>
            <select name="type" id="type" class="form-select" required>
                <option value="">Select Vacation Type</option>
                <option value="annual" {{ old('type') == 'annual' ? 'selected' : '' }}>Annual Leave</option>
                <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                <option value="unpaid" {{ old('type') == 'unpaid' ? 'selected' : '' }}>Unpaid Leave</option>
                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other Leave</option>
            </select>
        </div>

        <div class="date-fields-container">
            <div class="form-group">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-input" 
                       value="{{ old('start_date') }}" required>
            </div>

            <div class="form-group">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-input" 
                       value="{{ old('end_date') }}" required>
            </div>
        </div>

        <div class="duration-display" id="duration-display">
            Duration: 0 days
        </div>

        <div class="form-group">
            <label for="reason" class="form-label">Reason</label>
            <textarea name="reason" id="reason" class="form-textarea" rows="4" 
                      placeholder="Briefly explain the reason for your vacation">{{ old('reason') }}</textarea>
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
    <form method="GET" action="{{ route('vacations.index') }}" class="filter-form">
        <div class="form-group filter-group">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        
        <div class="form-group filter-group">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select">
                <option value="">All Types</option>
                <option value="annual" {{ request('type') == 'annual' ? 'selected' : '' }}>Annual</option>
                <option value="sick" {{ request('type') == 'sick' ? 'selected' : '' }}>Sick</option>
                <option value="unpaid" {{ request('type') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
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
            <a href="{{ route('vacations.index') }}" class="reset-btn">
                <i class="fas fa-sync-alt"></i> Reset
            </a>
        </div>
    </form>
</div>
@endif

<!-- Vacations List Section -->
@if(count($vacations) > 0)
<div class="vacations-list-container">
    <h3 style="margin-bottom: 1.5rem; color: #3a3541; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-list"></i>
        @if(auth()->user()->role === 'department_manager')
            My Department Vacation Requests
        @elseif(in_array(auth()->user()->role, ['admin', 'super_admin']))
            All Vacation Requests
        @else
            My Vacation Requests
        @endif
    </h3>
    
    <div style="overflow-x: auto;">
        <table class="vacations-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vacations as $vacation)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            @if($vacation->user->avatar)
                                <img src="{{ asset('storage/avatars/' . $vacation->user->avatar) }}" 
                                     alt="{{ $vacation->user->name }}" 
                                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #7367f0; color: white; display: flex; align-items: center; justify-content: center;">
                                    {{ substr($vacation->user->name, 0, 1) }}
                                </div>
                            @endif
                            <span>{{ $vacation->user->name }}</span>
                        </div>
                    </td>
                    <td>{{ ucfirst($vacation->type) }}</td>
                    <td>{{ \Carbon\Carbon::parse($vacation->start_date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($vacation->end_date)->format('M d, Y') }}</td>
                    <td>{{ $vacation->days_taken }} days</td>
                    <td class="status-{{ $vacation->status }}">
                        {{ ucfirst($vacation->status) }}
                        @if($vacation->approved_by)
                            <br><small style="font-size: 0.75rem; color: #6c757d;">by {{ $vacation->approver->name }}</small>
                        @endif
                    </td>
                    <td class="actions-cell">
                        @if(auth()->user()->role === 'department_manager' && 
                            $vacation->status == 'pending' && 
                            $vacation->user->department_id == auth()->user()->department_id &&
                            $vacation->user->id != auth()->user()->id &&
                            $vacation->user->role != 'department_manager')
                        <form action="{{ route('vacations.update', $vacation->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="action-btn approve-btn" title="Approve">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <form action="{{ route('vacations.update', $vacation->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="action-btn reject-btn" title="Reject">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                        @endif
                        
                        <button class="action-btn view-btn" title="View Details" onclick="showVacationDetails({{ $vacation->id }})">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($vacations->hasPages())
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $vacations->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@else
<div class="no-vacations">
    <i class="fas fa-inbox" style="font-size: 2rem; color: #6c757d; margin-bottom: 1rem;"></i>
    <h4>No vacation requests found</h4>
    <p>@if(in_array(auth()->user()->role, ['admin', 'super_admin', 'department_manager'])) No vacation requests match your filters @else You haven't submitted any vacation requests yet @endif</p>
</div>
@endif

<!-- Vacation Details Modal -->
<div id="vacationDetailsModal">
    <div class="modal-content">
        <h3 id="modalTitle" style="margin-bottom: 1.5rem; color: #3a3541; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-file-alt"></i>
            <span>Vacation Request Details</span>
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
    // Calculate duration between start and end dates
    function calculateDuration() {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const durationDisplay = document.getElementById('duration-display');

        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            const diffMs = end - start;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24)) + 1; // Include both start and end dates

            if (diffDays < 0) {
                durationDisplay.textContent = 'Duration: 0 days (End date must be after start date)';
                durationDisplay.style.color = '#dc3545';
                return;
            }

            durationDisplay.textContent = `Duration: ${diffDays} days`;
            durationDisplay.style.color = '#7367f0';
        } else {
            durationDisplay.textContent = 'Duration: 0 days';
            durationDisplay.style.color = '#7367f0';
        }
    }

    // Show vacation details in modal
    function showVacationDetails(vacationId) {
        fetch(`/vacations/${vacationId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('modalTitle').innerHTML = `
                    <i class="fas fa-file-alt"></i>
                    <span>Vacation Request #${data.id}</span>
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
                                ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                            </span>
                        </p>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <p><strong style="min-width: 120px; display: inline-block;">Start Date:</strong> ${new Date(data.start_date).toLocaleDateString()}</p>
                        <p><strong style="min-width: 120px; display: inline-block;">End Date:</strong> ${new Date(data.end_date).toLocaleDateString()}</p>
                        <p><strong style="min-width: 120px; display: inline-block;">Duration:</strong> ${data.days_taken} days</p>
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
                            <p><strong style="min-width: 120px; display: inline-block;">Approval Date:</strong> ${new Date(data.updated_at).toLocaleDateString()}</p>
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
                document.getElementById('vacationDetailsModal').style.display = 'flex';
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Failed to load vacation request details. Please try again.
                    </div>
                `;
                document.getElementById('vacationDetailsModal').style.display = 'flex';
            });
    }

    // Close modal
    function closeModal() {
        document.getElementById('vacationDetailsModal').style.display = 'none';
    }

    // Initialize event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        // Calculate duration when date inputs change
        if (startDate && endDate) {
            startDate.addEventListener('change', calculateDuration);
            endDate.addEventListener('change', calculateDuration);

            // Set minimum end date based on start date
            startDate.addEventListener('change', function() {
                if (this.value) {
                    endDate.min = this.value;
                }
            });

            // Calculate initial duration if values exist
            if (startDate.value || endDate.value) {
                calculateDuration();
            }
        }
    });
</script>

@endsection