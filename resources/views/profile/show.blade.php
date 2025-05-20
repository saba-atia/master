@extends('dash.dash')
@section('title', 'My Profile')
@section('contentdash')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10 col-md-12">
            <div class="card card-profile animated fadeIn">
                <div class="card-header bg-gradient-primary">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="mb-0 text-white">
                            <i class="fas fa-user-tie mr-2"></i>My Profile
                        </h3>
                        <div class="actions d-flex flex-wrap gap-2">
                            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-pencil-alt mr-1"></i> Edit Profile
                            </a>
                            <a href="{{ route('profile.password.change') }}" class="btn btn-sm btn-outline-white">
                                <i class="fas fa-key mr-1"></i> Change Password
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="profile-header text-center mb-5">
                        <div class="avatar-wrapper mx-auto mb-3 position-relative">
                            @if($user->photo_url && Storage::disk('public')->exists($user->photo_url))
                                <img src="{{ Storage::url($user->photo_url) }}" 
                                     alt="User Avatar" 
                                     class="rounded-circle img-thumbnail avatar-xl shadow">
                            @else
                                <div class="avatar-initials rounded-circle shadow-sm">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                            @endif
                            
                            @if($user->email_verified_at)
                                <span class="verified-badge" data-toggle="tooltip" title="Verified Account">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            @endif
                        </div>
                        <h2 class="mb-1">{{ $user->name }}</h2>
                        <div class="badge badge-pill bg-primary-soft text-primary px-3 py-1 mb-3">
                            @switch($user->role)
                                @case('admin') <i class="fas fa-shield-alt mr-1"></i> HR @break
                                @case('super_admin') <i class="fas fa-crown mr-1"></i> Manager @break
                                @case('department_manager') <i class="fas fa-user-tie mr-1"></i> Department Manager @break
                                @default <i class="fas fa-user mr-1"></i> Employee
                            @endswitch
                        </div>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-alt mr-1"></i> Member since {{ $user->created_at->format('M Y') }}
                        </p>
                    </div>

                    <!-- Personal Information - Full Width -->
                    <div class="card info-card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-id-card text-primary mr-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Email</dt>
                                        <dd class="col-sm-8">
                                            {{ $user->email }}
                                            @if(!$user->email_verified_at)
                                                <span class="badge badge-warning ml-2">Unverified</span>
                                            @endif
                                        </dd>

                                        <dt class="col-sm-4">Birth Date</dt>
                                        <dd class="col-sm-8">
                                            {{ $user->birth_date ? $user->birth_date->format('F j, Y') : 'Not set' }}
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-6">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Phone</dt>
                                        <dd class="col-sm-8">
                                            {{ $user->phone ?? 'Not set' }}
                                        </dd>

                                        <dt class="col-sm-4">Address</dt>
                                        <dd class="col-sm-8">
                                            {{ $user->address ?? 'Not set' }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Work Information - Full Width -->
                    <!-- In the Work Information section -->
<div class="card info-card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-briefcase text-primary mr-2"></i>Work Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-nowrap">Department</dt> <!-- Added text-nowrap class -->
                    <dd class="col-sm-8">
                        {{ $user->department->name ?? 'Not assigned' }}
                    </dd>
                </dl>
            </div>
           
        </div>
    </div>
</div>

                    <!-- Emergency Contact - Full Width -->
                    <div class="card info-card">
                        <div class="card-header bg-danger-light">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-danger mr-2"></i>Emergency Contact</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">
                                {{ $user->emergency_contact ?? 'Not set' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #ff9635;
        --primary-dark: #0338d4;
        --primary-light: #f8f9fc;
        --danger-light: #fce8e8;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .bg-primary-soft {
        background-color: var(--primary-light);
    }

    .bg-danger-light {
        background-color: var(--danger-light);
    }

    .card-profile {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        transition: all 0.3s ease;
    }

    .card-profile:hover {
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
    }

    .avatar-wrapper {
        position: relative;
        width: fit-content;
        margin: 0 auto;
    }

    .avatar-xl {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid #fff;
    }

    .avatar-initials {
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        font-size: 2.5rem;
        font-weight: bold;
        border: 4px solid #fff;
        border-radius: 50%;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .verified-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: #28a745;
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        font-size: 0.8rem;
    }

    .profile-header {
        position: relative;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }

    .profile-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        border-radius: 3px;
    }

    .info-card {
        border-radius: 0.35rem;
        border: 1px solid #e3e6f0;
    }

    .info-card .card-header {
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1.25rem;
    }

    .info-card .card-body {
        padding: 1.25rem;
    }

    dl.row dt {
        font-weight: 500;
        color: #6c757d;
    }

    dl.row dd {
        font-weight: 600;
        color: #5a5c69;
    }

    .animated {
        animation-duration: 0.5s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .fadeIn {
        animation-name: fadeIn;
    }

    @media (max-width: 768px) {
        .card-header .actions {
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .card-header .actions .btn {
            width: 100%;
            margin-left: 0 !important;
        }
        
        dl.row dt, dl.row dd {
            padding: 0.25rem;
        }
        
        .avatar-xl, .avatar-initials {
            width: 100px;
            height: 100px;
            font-size: 2rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection