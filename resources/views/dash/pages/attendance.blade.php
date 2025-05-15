@extends('dash.dash')
@section('title', 'attendance')
@section('contentdash')

@php
    $user = auth()->user();
    $branches = App\Models\Branch::all();
    $todayAttendance = $user->attendances()->whereDate('date', today())->first();
@endphp

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <!-- Card Header -->
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            <i class="material-icons opacity-10 me-2">schedule</i>
                            @if (auth()->user()->isAdminOrSuperAdmin())
                                سجلات الحضور للجميع
                            @else
                                سجلات الحضور الخاصة بي
                            @endif
                        </h6>
                    </div>
                </div>

                <!-- Punch In/Out Section -->
                <div class="card-body p-3">
                    <div class="action-container text-center p-3 rounded bg-light mb-4">
                        @if (!$todayAttendance)
                            <form id="attendanceForm" method="POST" action="{{ route('attendance.store') }}">
                                @csrf
                                <div class="form-group mb-3 text-start">
                                    <label for="branchSelect" class="form-label fw-bold">اختر الفرع</label>
                                    <select name="branch_id" id="branchSelect" class="form-select" required>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }} - {{ $branch->address }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">

                                <button type="button" id="checkInBtn" class="btn btn-primary w-100 py-3 fw-bold">
                                    <i class="material-icons opacity-10 me-1">login</i>
                                    تسجيل الحضور
                                </button>

                                <div id="locationStatus" class="mt-3"></div>
                            </form>
                        @elseif(!$todayAttendance->check_out)
                            <form id="punchOutForm" action="{{ route('attendance.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="branch_id" value="{{ $todayAttendance->branch_id }}">
                                <div class="alert alert-info text-start">
                                    <i class="material-icons me-2">business</i>
<strong>الفرع الحالي:</strong> 
@if($todayAttendance->branch)
    {{ $todayAttendance->branch->name }}
@else
    <span class="text-danger">غير محدد</span>
@endif                                </div>
                                <button type="button" id="checkOutBtn" class="btn btn-danger w-100 py-3 fw-bold">
                                    <i class="material-icons opacity-10 me-1">logout</i>
                                    تسجيل الانصراف
                                </button>
                                <div id="checkOutStatus" class="mt-3"></div>
                            </form>
                        @else
                            <div class="alert alert-success text-start">
                                <div class="d-flex align-items-center">
                                    <i class="material-icons me-2">done_all</i>
                                    <div>
                                        <strong>حالة اليوم:</strong>
                                        <span class="badge bg-success">{{ $todayAttendance->status }}</span>
                                        <div class="mt-2">
                                            <span class="badge bg-dark me-2">
                                                <i class="material-icons md-18">login</i>
                                                {{ $todayAttendance->check_in->format('h:i A') }}
                                            </span>
                                            <span class="badge bg-dark me-2">
                                                <i class="material-icons md-18">logout</i>
                                                {{ $todayAttendance->check_out->format('h:i A') }}
                                            </span>
                                            <span class="badge bg-dark">
                                                <i class="material-icons md-18">access_time</i>
                                                {{ $todayAttendance->working_hours }} ساعة
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Attendance Records Table -->
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead class="bg-gray-200">
                                <tr>
                                    @if (auth()->user()->isAdminOrSuperAdmin())
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder">الموظف</th>
                                    @endif
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">التاريخ</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">الحضور</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">الانصراف</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">الحالة</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">ساعات العمل</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder">الفرع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendances as $attendance)
                                    <tr>
                                        @if (auth()->user()->isAdminOrSuperAdmin())
                                            <td class="text-sm">{{ $attendance->user->name }}</td>
                                        @endif
                                        <td class="text-sm">{{ $attendance->date->format('Y-m-d') }}</td>
                                        <td class="text-sm">{{ $attendance->check_in?->format('h:i A') ?? '--' }}</td>
                                        <td class="text-sm">{{ $attendance->check_out?->format('h:i A') ?? '--' }}</td>
                                        <td class="text-sm">
                                            <span class="badge bg-{{ $attendance->status == 'Completed' ? 'success' : ($attendance->status == 'In Progress' ? 'warning' : 'danger') }}">
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                        <td class="text-sm">{{ $attendance->working_hours ?? '--' }}</td>
                                        <td class="text-sm">{{ $attendance->branch->name ?? '--' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isAdminOrSuperAdmin() ? 7 : 6 }}" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center text-muted">
                                                <i class="material-icons opacity-10 mb-2" style="font-size: 2.5rem;">hourglass_empty</i>
                                                لا توجد سجلات حضور
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

<style>
    /* تحسينات عامة للاستجابة */
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* تحسينات الجدول */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .attendance-table {
        width: 100%;
        min-width: 600px;
    }

    /* أعمدة الجدول مع أحجام نسبية */
    .employee-col { width: 25%; min-width: 200px; }
    .date-col { width: 10%; min-width: 80px; }
    .time-col { width: 10%; min-width: 90px; }
    .status-col { width: 12%; min-width: 100px; }
    .hours-col { width: 8%; min-width: 70px; }
    .branch-col { width: 15%; min-width: 120px; }
    .location-col { width: 20%; min-width: 150px; }

    /* Avatar Styles */
    .avatar-wrapper {
        position: relative;
        width: 60px;
        height: 60px;
        min-width: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-table {
        width: 100%;
        height: 100%;
        max-width: 60px;
        max-height: 60px;
        object-fit: contain;
        border: 3px solid #fff;
        background-color: #f8f9fa;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .avatar-table:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .verified-badge-sm {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #28a745;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        font-size: 0.6rem;
    }
    
    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 0.3em 0.6em;
        font-size: 0.7em;
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
    
    .action-container {
        background-color: #f8f9fa;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    @media (max-width: 768px) {
        .attendance-table {
            min-width: 100%;
        }
        
        .attendance-table thead {
            display: none;
        }
        
        .attendance-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.5rem;
        }
        
        .attendance-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #f0f0f0;
            width: 100% !important;
        }
        
        .attendance-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #555;
            margin-right: 1rem;
            flex: 0 0 120px;
        }
        
        .attendance-table td:last-child {
            border-bottom: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check In Process
    const checkInBtn = document.getElementById('checkInBtn');
    if (checkInBtn) {
        checkInBtn.addEventListener('click', handleAttendanceAction.bind(null, 'checkIn'));
    }

    // Check Out Process
    const checkOutBtn = document.getElementById('checkOutBtn');
    if (checkOutBtn) {
        checkOutBtn.addEventListener('click', handleAttendanceAction.bind(null, 'checkOut'));
    }

    function handleAttendanceAction(actionType, event) {
        const btn = event.target;
        const form = btn.closest('form');
        const statusDiv = document.getElementById(actionType === 'checkIn' ? 'locationStatus' : 'checkOutStatus');
        
        btn.disabled = true;
        btn.innerHTML = `<i class="material-icons opacity-10 me-1">hourglass_top</i> جاري المعالجة...`;
        
        if (statusDiv) {
            statusDiv.innerHTML = '';
        }

        if (!navigator.geolocation) {
            showStatus(statusDiv, 'المتصفح لا يدعم تحديد الموقع', 'error');
            resetButton(btn, actionType);
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                
                showStatus(statusDiv, `
                    <div class="alert alert-success text-start p-2">
                        <i class="material-icons opacity-10 me-1">location_on</i>
                        <strong>تم تحديد الموقع:</strong><br>
                        خط العرض: ${position.coords.latitude.toFixed(6)}<br>
                        خط الطول: ${position.coords.longitude.toFixed(6)}
                    </div>
                `);
                
                setTimeout(() => {
                    form.submit();
                }, 1000);
            },
            (error) => {
                let message = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message = 'تم رفض طلب الموقع. يرجى السماح بالوصول إلى الموقع في إعدادات المتصفح.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message = 'معلومات الموقع غير متاحة. يرجى التأكد من اتصال الإنترنت.';
                        break;
                    case error.TIMEOUT:
                        message = 'انتهت مهلة طلب الموقع. يرجى المحاولة مرة أخرى في مكان مفتوح.';
                        break;
                    default:
                        message = 'حدث خطأ غير متوقع: ' + error.message;
                }
                
                showStatus(statusDiv, `
                    <div class="alert alert-danger text-start p-2">
                        <i class="material-icons opacity-10 me-1">error</i>
                        ${message}
                    </div>
                `);
                
                resetButton(btn, actionType);
            },
            {
                enableHighAccuracy: true,
                timeout: 30000,
                maximumAge: 0
            }
        );
    }

    function showStatus(element, message, type = 'info') {
        if (!element) return;
        element.innerHTML = message;
    }

    function resetButton(button, actionType) {
        if (!button) return;
        
        button.disabled = false;
        if (actionType === 'checkIn') {
            button.innerHTML = '<i class="material-icons opacity-10 me-1">login</i> تسجيل الحضور';
        } else {
            button.innerHTML = '<i class="material-icons opacity-10 me-1">logout</i> تسجيل الانصراف';
        }
    }
});
</script>
@endsection