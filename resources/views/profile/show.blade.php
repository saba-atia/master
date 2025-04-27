@extends('dash.dash')
@section('title', 'User Profile')

@section('contentdash')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">User Profile</div>

                <div class="card-body">
                    @if (session('success') == 'update_success')
                    @push('scripts')
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Profile updated',
                            text: 'Your profile has been updated successfully!',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    </script>
                    @endpush
                    @endif
                    
                    <div class="text-center mb-4">
                        <div class="avatar-circle" style="background-color: #4e73df; width: 150px; height: 150px; margin: 0 auto;">
                            <span class="initials" style="color: white; font-size: 48px; line-height: 150px;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Name:</label>
                        <p class="form-control-static">{{ $user->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <p class="form-control-static">{{ $user->email }}</p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Profile
                        </a>
                        
                        <a href="{{ route('password.change') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-key me-1"></i> Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .initials {
        font-weight: bold;
    }
</style>

@endsection