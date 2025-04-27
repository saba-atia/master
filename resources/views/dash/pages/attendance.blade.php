@extends('dash.dash')

@section('contentdash')

@php
    $user = auth()->user();
    $from = request('from');
    $to = request('to');

    $query = $user->attendances();

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
                                My Attendance Records
                            </h6>
                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                                <a href="{{ route('branches.map') }}" class="btn btn-sm btn-light me-3">
                                    <i class="material-icons opacity-10 me-1">map</i>
                                    Manage Branch Locations
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card-body px-4 pb-2">
                    <div class="px-3 mb-3">
                        <form method="GET" class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label for="from" class="form-label">From Date</label>
                                <input type="date" name="from" id="from" value="{{ request('from') }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="to" class="form-label">To Date</label>
                                <input type="date" name="to" id="to" value="{{ request('to') }}" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('attendance.index') }}" class="btn btn-secondary w-100">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Punch In/Out Section with Geolocation -->
                    <div class="action-container text-center py-3 rounded bg-light mb-4">
                        @php
                            $todayAttendance = $user->attendances()
                                ->whereDate('date', today())
                                ->first();
                        @endphp
                        
                        @if(!$todayAttendance)
                            <form id="punchInForm" action="{{ route('attendance.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                <input type="hidden" name="action" value="in">
                                <button type="button" onclick="getLocation('punchInForm')" class="btn btn-success btn-lg shadow-sm">
                                    <i class="material-icons me-2">fingerprint</i> 
                                    <span class="fw-bold">Punch In</span>
                                    <small class="d-block mt-1">{{ now()->format('h:i A, M d') }}</small>
                                </button>
                            </form>
                        @elseif(!$todayAttendance->check_out)
                            <form id="punchOutForm" action="{{ route('attendance.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                <input type="hidden" name="action" value="out">
                                <button type="button" onclick="getLocation('punchOutForm')" class="btn btn-danger btn-lg shadow-sm">
                                    <i class="material-icons me-2">exit_to_app</i>
                                    <span class="fw-bold">Punch Out</span>
                                    <small class="d-block mt-1">{{ now()->format('h:i A, M d') }}</small>
                                </button>
                            </form>
                        @else
                        <div class="alert alert-success alert-dismissible fade show">
                            <div class="d-flex align-items-center">
                                <i class="material-icons me-2">done_all</i>
                                <strong>Today's Attendance: 
                                    <span class="@if($todayAttendance->status == 'Completed') text-success
                                              @elseif($todayAttendance->status == 'In Progress') text-warning
                                              @else text-danger @endif">
                                        {{ $todayAttendance->status }}
                                    </span>
                                </strong>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-gradient-dark">
                                    In: {{ $todayAttendance->check_in ? Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : '--' }}
                                </span>
                                <span class="badge bg-gradient-dark ms-2">
                                    Out: {{ $todayAttendance->check_out ? Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') : '--' }}
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
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Date</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Punch In</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Punch Out</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Working Hours</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                <tr>
                                    <td>
                                        <span class="text-xs font-weight-bold">{{ $attendance->date->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        @if($attendance->check_in)
                                            <span class="badge bg-success-light text-success">
                                                {{ $attendance->check_in->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-xs text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->check_out)
                                            <span class="badge bg-danger-light text-danger">
                                                {{ $attendance->check_out->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-xs text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($attendance->status == 'Completed') bg-success-light text-success
                                            @elseif($attendance->status == 'In Progress') bg-warning-light text-warning
                                            @elseif($attendance->status == 'Not Completed') bg-danger-light text-danger
                                            @else bg-secondary-light text-secondary
                                            @endif">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($attendance->working_hours !== null)
                                            <span class="text-xs font-weight-bold">
                                                {{ $attendance->working_hours }}h
                                            </span>
                                        @else
                                            <span class="text-xs text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->latitude && $attendance->longitude)
                                            <a href="https://maps.google.com/?q={{ $attendance->latitude }},{{ $attendance->longitude }}" target="_blank" class="text-info">
                                                <i class="material-icons">location_on</i>
                                            </a>
                                        @else
                                            <span class="text-xs text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="material-icons opacity-10 mb-2" style="font-size: 3rem;">hourglass_empty</i>
                                            <h6 class="text-sm text-muted">No attendance records found</h6>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function getLocation(formId) {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser');
        return;
    }
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.getElementById(formId).submit();
        },
        function(error) {
            alert('Error getting your location: ' + error.message);
        },
        { enableHighAccuracy: true }
    );
}
</script>

<style>
    .bg-success-light { background-color: #e6ffea; }
    .bg-warning-light { background-color: #fffae6; }
    .bg-danger-light { background-color: #ffebee; }
    .bg-secondary-light { background-color: #f5f5f5; }
    .text-success { color: #28a745; }
    .text-warning { color: #ffc107; }
    .text-danger { color: #dc3545; }
    .text-secondary { color: #6c757d; }
</style>

@endsection