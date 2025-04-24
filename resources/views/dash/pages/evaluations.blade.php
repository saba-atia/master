@extends('dash.dash')
@section('title', 'Evaluations')
@section('contentdash')



<style>
    body {
        background-color: #f8f9fa;
        padding-top: 40px;
    }
    .evaluation-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        border: none;
    }
    .employee-card {
        border-top: 4px solid #4e73df;
    }
    .admin-card {
        border-top: 4px solid #1cc88a;
    }
    .super-admin-card {
        border-top: 4px solid #f6c23e;
    }
    .alert-card {
        border-top: 4px solid #e74a3b;
    }
    .card-title {
        font-weight: 600;
        margin-bottom: 20px;
    }
    .icon-lg {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(auth()->user()->role === 'employee')
                <div class="card evaluation-card employee-card">
                    <div class="card-body text-center py-5">
                        <div class="icon-lg">📋</div>
                        <h2 class="card-title text-primary">Your Performance Evaluations</h2>
                        <p class="card-text text-muted">
                            Review your monthly evaluations to improve performance and develop your professional skills
                        </p>
                        <a href="#" class="btn btn-primary mt-3">View Evaluations</a>
                    </div>
                </div>

            @elseif(auth()->user()->role === 'admin')
                <div class="card evaluation-card admin-card">
                    <div class="card-body text-center py-5">
                        <div class="icon-lg">📊</div>
                        <h2 class="card-title text-success">HR Evaluation Dashboard</h2>
                        <p class="card-text text-muted">
                            Manage employee performance evaluations, generate reports and track career development
                        </p>
                        <div class="mt-4">
                            <a href="#" class="btn btn-success me-2">Manage Evaluations</a>
                            <a href="#" class="btn btn-outline-secondary">Generate Reports</a>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->role === 'super_admin')
                <div class="card evaluation-card super-admin-card">
                    <div class="card-body text-center py-5">
                        <div class="icon-lg">👁️‍🗨️</div>
                        <h2 class="card-title text-warning">Management Overview - Evaluations</h2>
                        <p class="card-text text-muted">
                            Monitor evaluation quality, analyze overall performance and make strategic decisions to improve productivity
                        </p>
                        <div class="mt-4">
                            <a href="#" class="btn btn-warning me-2">Performance Analytics</a>
                            <a href="#" class="btn btn-outline-dark">Quality Metrics</a>
                        </div>
                    </div>
                </div>

            @else
                <div class="card evaluation-card alert-card">
                    <div class="card-body text-center py-5">
                        <div class="icon-lg">⛔</div>
                        <h2 class="card-title text-danger">Access Denied</h2>
                        <p class="card-text text-muted">
                            You don't have sufficient permissions to access this page. Please contact system administrators
                        </p>
                        <a href="{{ url('/') }}" class="btn btn-danger mt-3">Return to Homepage</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->


@endsection