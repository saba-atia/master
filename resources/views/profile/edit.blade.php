@extends('dash.dash')
@section('title','Edit Profile')
@section('contentdash')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card elevation-3">
                <div class="card-header bg-primary-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0 text-white">
                            <i class="fas fa-user-edit mr-2"></i>Edit Profile
                        </h3>
                        <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Profile
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-5">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Please fix the following errors
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="text-center mb-5">
                            <div class="avatar-upload mx-auto">
                                @if($user->photo_url && Storage::disk('public')->exists($user->photo_url))
                                    <div class="avatar-preview rounded-circle shadow-sm" 
                                         style="background-image: url('{{ Storage::url($user->photo_url) }}');">
                                    </div>
                                @else
                                    <div class="avatar-initials rounded-circle shadow-sm">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                @endif
                                
                                <div class="avatar-edit">
                                    <input type="file" id="photo" name="photo" class="d-none" accept="image/*">
                                    <label for="photo" class="btn btn-primary btn-circle">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                    @if($user->photo_url)
                                    <button type="button" class="btn btn-danger btn-circle ml-2" id="removePhoto">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <input type="hidden" name="remove_photo" id="remove_photo" value="0">
                                    @endif
                                </div>
                                <div id="photo-error" class="invalid-feedback d-block text-center"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" id="name" name="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" id="email" name="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="birth_date" class="form-label">Date of Birth</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" id="birth_date" name="birth_date" 
                                               class="form-control @error('birth_date') is-invalid @enderror" 
                                               value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                                        @error('birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input type="tel" id="phone" name="phone" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select id="department_id" name="department_id" class="form-control select2 @error('department_id') is-invalid @enderror">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="branch_id" class="form-label">Branch</label>
                                    <select id="branch_id" name="branch_id" class="form-control select2 @error('branch_id') is-invalid @enderror">
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" 
                                                {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="emergency_contact" class="form-label">Emergency Contact</label>
                            <textarea id="emergency_contact" name="emergency_contact" class="form-control @error('emergency_contact') is-invalid @enderror" rows="2">{{ old('emergency_contact', $user->emergency_contact) }}</textarea>
                            @error('emergency_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-5 pt-3 border-top">
                            <div class="d-flex justify-content-between">
                                <button type="reset" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-undo mr-2"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                    <i class="fas fa-save mr-2"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Avatar Styles */
    .avatar-upload {
        position: relative;
        max-width: 160px;
        margin: 0 auto 2rem;
    }

    .avatar-preview {
        width: 140px;
        height: 140px;
        background-size: cover;
        background-position: center;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .avatar-initials {
        width: 140px;
        height: 140px;
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
        margin: 0 auto;
    }

    .avatar-upload .avatar-edit {
        position: absolute;
        bottom: -20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
    }

    .btn-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .btn-circle:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    /* Form Styles */
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .input-group {
        margin-bottom: 1.25rem;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .form-control {
        border-left: 0;
        height: calc(1.5em + 1rem + 2px);
        padding: 0.5rem 1rem;
    }

    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 1rem + 2px) !important;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem !important;
    }

    /* Card Styles */
    .card.elevation-3 {
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .card-header.bg-primary-dark {
        background: linear-gradient(135deg, #ff9635, #0338d4);
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .avatar-preview, .avatar-initials {
            width: 120px;
            height: 120px;
            font-size: 2rem;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview functionality
    const photoInput = document.getElementById('photo');
    const avatarContainer = document.querySelector('.avatar-upload');
    let avatarPreview = document.querySelector('.avatar-preview, .avatar-initials');
    const removePhotoBtn = document.getElementById('removePhoto');
    const removePhotoFlag = document.getElementById('remove_photo');

    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // If currently showing initials, replace with image preview
                    if (avatarPreview.classList.contains('avatar-initials')) {
                        const newPreview = document.createElement('div');
                        newPreview.className = 'avatar-preview rounded-circle shadow-sm';
                        newPreview.style.backgroundImage = `url(${e.target.result})`;
                        avatarContainer.replaceChild(newPreview, avatarPreview);
                        avatarPreview = newPreview;
                    } else {
                        avatarPreview.style.backgroundImage = `url(${e.target.result})`;
                    }
                    
                    if (removePhotoFlag) {
                        removePhotoFlag.value = '0';
                    }
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete the current photo?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Replace image preview with initials
                    if (avatarPreview.classList.contains('avatar-preview')) {
                        const initials = '{{ substr($user->name, 0, 2) }}';
                        const newInitials = document.createElement('div');
                        newInitials.className = 'avatar-initials rounded-circle shadow-sm';
                        newInitials.textContent = initials;
                        avatarContainer.replaceChild(newInitials, avatarPreview);
                        avatarPreview = newInitials;
                    }
                    
                    if (photoInput) {
                        photoInput.value = '';
                    }
                    if (removePhotoFlag) {
                        removePhotoFlag.value = '1';
                    }
                    
                    Swal.fire(
                        'Deleted!',
                        'The photo has been deleted.',
                        'success'
                    );
                }
            });
        });
    }

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Show success message after form submission
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Updated successfully!',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK'
        });
    @endif
});
</script>
@endsection