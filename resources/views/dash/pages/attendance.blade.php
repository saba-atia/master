@extends('dash.dash')

@section('contentdash')

@php
    $user = auth()->user();
    $from = request('from');
    $to = request('to');
    $branches = App\Models\Branch::all();

    if ($user->isAdminOrSuperAdmin()) {
        $query = \App\Models\Attendance::with(['user', 'branch']);
    } else {
        $query = $user->attendances()->with('branch');
    }

    if ($from && $to) {
        $query = $query->whereBetween('date', [$from, $to]);
    }

    $attendances = $query->latest()->get();
@endphp

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <!-- Card Header -->
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-white text-capitalize ps-3">
                                <i class="material-icons opacity-10 me-2">schedule</i>
                                @if(auth()->user()->isAdminOrSuperAdmin())
                                    All Employees Attendance Records
                                @else
                                    My Attendance Records
                                @endif
                            </h6>
                            @if(auth()->user()->isAdminOrSuperAdmin())
                                <a href="{{ route('branches.index') }}" class="btn btn-sm btn-light me-3">
                                    <i class="material-icons opacity-10 me-1">business</i>
                                    Manage Branches
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Punch In/Out Section -->
                <div class="card-body px-4 pb-2">
                    <div class="action-container text-center py-3 rounded bg-light mb-4">
                        @php
                            $todayAttendance = auth()->user()->attendances()
                                ->whereDate('date', today())
                                ->first();
                        @endphp
                        
                        @if(!$todayAttendance)
                            <form id="punchInForm" action="{{ route('attendance.store') }}" method="POST">
                                @csrf
                                <div class="row justify-content-center">
                                    <div class="col-md-6 mb-3">
                                        <label for="branch_id" class="form-label">Select Branch</label>
                                        <select name="branch_id" id="branch_id" class="form-select" required>
                                            <option value="">-- Select Branch --</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg shadow-sm">
                                    <i class="material-icons me-2">fingerprint</i> 
                                    <span class="fw-bold">Check In</span>
                                    <small class="d-block mt-1">{{ now()->format('h:i A, M d') }}</small>
                                </button>
                            </form>
                        @elseif(!$todayAttendance->check_out)
                            <form id="punchOutForm" action="{{ route('attendance.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $todayAttendance->branch_id }}">
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons me-2">business</i>
                                        <strong>Current Branch: {{ $todayAttendance->branch->name }}</strong>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-danger btn-lg shadow-sm">
                                    <i class="material-icons me-2">exit_to_app</i>
                                    <span class="fw-bold">Check Out</span>
                                    <small class="d-block mt-1">{{ now()->format('h:i A, M d') }}</small>
                                </button>
                            </form>
                        @else
                        <div class="alert alert-success alert-dismissible fade show">
                            <div class="d-flex align-items-center">
                                <i class="material-icons me-2">done_all</i>
                                <strong>Today's Status: 
                                    <span class="@if($todayAttendance->status == 'Completed') text-success
                                              @elseif($todayAttendance->status == 'In Progress') text-warning
                                              @else text-danger @endif">
                                        {{ $todayAttendance->status }}
                                    </span>
                                </strong>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-gradient-dark">
                                    Check-in: {{ $todayAttendance->check_in ? Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : '--' }}
                                </span>
                                <span class="badge bg-gradient-dark ms-2">
                                    Check-out: {{ $todayAttendance->check_out ? Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') : '--' }}
                                </span>
                                <span class="badge bg-gradient-dark ms-2">
                                    Branch: {{ $todayAttendance->branch->name ?? '--' }}
                                </span>
                                <span class="badge bg-gradient-dark ms-2">
                                    Hours: {{ $todayAttendance->working_hours ? number_format($todayAttendance->working_hours, 2).'h' : '--' }}
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Attendance Records Table -->
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    @if(auth()->user()->isAdminOrSuperAdmin())
                                    <th>Employee</th>
                                    @endif
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Working Hours</th>
                                    <th>Branch</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($attendances->count() > 0)
                                    @foreach($attendances as $attendance)
                                    <tr>
                                        @if(auth()->user()->isAdminOrSuperAdmin())
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    <img src="{{ $attendance->user && $attendance->user->avatar 
                                                              ? asset('storage/avatars/'.$attendance->user->avatar)
                                                              : asset('images/default-avatar.png') }}" 
                                                         class="avatar-img"
                                                         alt="{{ $attendance->user ? $attendance->user->name : 'User' }}"
                                                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}'">
                                                </div>
                                                <span class="text-sm font-weight-bold">
                                                    {{ $attendance->user ? $attendance->user->name : 'Deleted User' }}
                                                </span>
                                            </div>
                                        </td>
                                        @endif
                                        <td>{{ $attendance->date->format('M d, Y') }}</td>
                                        <td>{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : '--' }}</td>
                                        <td>{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : '--' }}</td>
                                        <td>
                                            <span class="status-badge 
                                                @if($attendance->status == 'Completed') status-completed
                                                @elseif($attendance->status == 'In Progress') status-in-progress
                                                @else status-missing @endif">
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                        <td class="working-hours">{{ $attendance->working_hours ? number_format($attendance->working_hours, 2).'h' : '--' }}</td>
                                        <td class="branch-name" title="{{ $attendance->branch ? $attendance->branch->name : '--' }}">
                                            {{ $attendance->branch ? $attendance->branch->name : '--' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="{{ auth()->user()->isAdminOrSuperAdmin() ? 7 : 6 }}" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="material-icons opacity-10 mb-2" style="font-size: 2.5rem;">hourglass_empty</i>
                                                <span class="text-muted">No attendance records found</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ستايلات الجدول المحسنة */
    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 1rem;
        background-color: transparent;
    }
    
    .table thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        padding: 1rem 0.75rem;
        vertical-align: middle;
        z-index: 10;
    }
    
    .table tbody td {
        padding: 0.75rem;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
        font-size: 0.875rem;
        color: #495057;
    }
    
    .table tbody tr:hover {
        background-color: rgba(115, 103, 240, 0.04);
    }
    
    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    /* تحسين مظهر الصور */
    .avatar {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        overflow: hidden;
    }
    
    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.1);
    }
    
    /* تحسين مظهر الحالة */
    .status-badge {
        display: inline-block;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 600;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    
    .status-completed {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    
    .status-in-progress {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }
    
    .status-missing {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    
    /* تحسين مظهر ساعات العمل */
    .working-hours {
        font-weight: 600;
        color: #5e5873;
    }
    
    /* تحسين مظهر الفرع */
    .branch-name {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* تأثيرات التفاعل */
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    /* التنسيق العام للجدول */
    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    /* ستايلات الأزرار */
    .btn-success, .btn-danger {
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }
</style>
@endsection