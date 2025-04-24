@extends('dash.dash')
@section('title', 'Reports')
@section('contentdash')

<style>
    .role-card {
        border-radius: 10px;
        transition: transform 0.3s ease;
    }
    .role-card:hover {
        transform: translateY(-5px);
    }
    .employee-card {
        border-left: 5px solid #4e73df;
    }
    .admin-card {
        border-left: 5px solid #1cc88a;
    }
    .super-admin-card {
        border-left: 5px solid #f6c23e;
    }
    .unauthorized-card {
        border-left: 5px solid #e74a3b;
    }
    .report-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(auth()->user()->role == 'employee')
                <div class="card shadow-sm role-card employee-card mb-4">
                    <div class="card-body text-center p-4">
                        <div class="report-icon">📄</div>
                        <h2 class="card-title text-primary mb-3">My Reports</h2>
                        <p class="card-text text-muted mb-4">
                            Review your attendance, leave records, and performance history.
                        </p>
                        <div class="d-grid gap-2 d-md-block">
                            <button class="btn btn-primary me-md-2">View Attendance</button>
                            <button class="btn btn-outline-primary">Performance History</button>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->role == 'admin')
                <div class="card shadow-sm role-card admin-card mb-4">
                    <div class="card-body text-center p-4">
                        <div class="report-icon">📊</div>
                        <h2 class="card-title text-success mb-3">HR Reports Dashboard</h2>
                        <p class="card-text text-muted mb-4">
                            Monitor attendance and leave reports, track employee evaluations for HR analysis.
                        </p>
                        <div class="d-grid gap-2 d-md-block">
                            <button class="btn btn-success me-md-2">Employee Reports</button>
                            <button class="btn btn-outline-success me-md-2">Attendance Analysis</button>
                            <button class="btn btn-outline-secondary">Export Data</button>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->role == 'super_admin')
                <div class="card shadow-sm role-card super-admin-card mb-4">
                    <div class="card-body text-center p-4">
                        <div class="report-icon">📈</div>
                        <h2 class="card-title text-warning mb-3">Management Reports Center</h2>
                        <p class="card-text text-muted mb-4">
                            Analyze overall performance reports, compare departments to evaluate progress and goal achievement.
                        </p>
                        <div class="d-grid gap-2 d-md-block">
                            <button class="btn btn-warning me-md-2">Performance Overview</button>
                            <button class="btn btn-outline-warning me-md-2">Department Comparison</button>
                            <button class="btn btn-dark">Strategic Insights</button>
                        </div>
                    </div>
                </div>

            @else
                <div class="card shadow-sm role-card unauthorized-card mb-4">
                    <div class="card-body text-center p-4">
                        <div class="report-icon">⚠️</div>
                        <h2 class="card-title text-danger mb-3">Unauthorized Access</h2>
                        <p class="card-text text-muted mb-4">
                            You don't have permission to view this content. Please contact your system administrator.
                        </p>
                        <a href="/" class="btn btn-danger">Return to Home</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->

@endsection
