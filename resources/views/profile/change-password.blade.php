@extends('dash.dash')
@section('title', 'Edit Profile')
@section('contentdash')

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#profile" data-bs-toggle="tab">
                                <i class="fas fa-user-edit me-2"></i>Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('password.change') }}">
                                <i class="fas fa-key me-2"></i>Password
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="profile">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row mb-4">
                                    <div class="col-md-4 text-center">
                                        <div class="profile-picture-container mb-3">
                                            <img src="{{ auth()->user()->photo_url ?? asset('assets/img/default-avatar.png') }}" 
                                                 class="profile-picture rounded-circle" 
                                                 alt="Profile picture">
                                            <label for="imageUpload" class="profile-picture-edit">
                                                <i class="fas fa-camera"></i>
                                                <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="name" class="form-control" 
                                                   value="{{ old('name', auth()->user()->name) }}" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" 
                                                   value="{{ old('email', auth()->user()->email) }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone" class="form-control" 
                                                   value="{{ old('phone', auth()->user()->phone) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Birth Date</label>
                                            <input type="date" name="birth_date" class="form-control" 
                                                   value="{{ old('birth_date', auth()->user()->birth_date ? auth()->user()->birth_date->format('Y-m-d') : '') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" 
                                           value="{{ old('address', auth()->user()->address) }}">
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label">About Me</label>
                                    <textarea name="bio" class="form-control" rows="3"
                                              placeholder="Tell us about yourself...">{{ old('bio', auth()->user()->bio) }}</textarea>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Profile picture upload script same as show.blade.php
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.querySelector('.profile-picture').src = event.target.result;
                
                const formData = new FormData();
                formData.append('photo', file);
                formData.append('_token', '{{ csrf_token() }}');
                
                fetch('{{ route("profile.photo") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                    } else {
                        toastr.error(data.message || 'Error updating profile picture');
                        document.querySelector('.profile-picture').src = '{{ auth()->user()->photo_url ?? asset('assets/img/default-avatar.png') }}';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while uploading');
                    document.querySelector('.profile-picture').src = '{{ auth()->user()->photo_url ?? asset('assets/img/default-avatar.png') }}';
                });
            };
            reader.readAsDataURL(file);
        }
    });
</script>

@endsection