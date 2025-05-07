@extends('dash.dash')
@section('title', 'My Profile')
@section('contentdash')

<style>
    :root {
        --birthday-primary: #FF6B6B;
        --birthday-secondary: #FFA3A3;
        --birthday-light: #FFF5F5;
    }
    
    .profile-header {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
    
    .profile-picture-container {
        position: relative;
        width: fit-content;
        margin: 0 auto;
        transition: all 0.3s ease;
    }
    
    .profile-picture {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .profile-picture-edit {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: var(--primary-color);
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    
    .profile-picture-edit:hover {
        transform: scale(1.1);
        background: var(--primary-hover);
    }
    
    .birthday-card {
        background: linear-gradient(135deg, var(--birthday-primary) 0%, var(--birthday-secondary) 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .birthday-card::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512' fill='rgba(255,255,255,0.1)'%3E%3Cpath d='M0 190.9V185.1C0 115.2 50.52 55.58 119.4 44.1C164.1 36.51 211.4 51.37 244 84.02L256 96L267.1 84.02C300.6 51.37 347 36.51 392.6 44.1C461.5 55.58 512 115.2 512 185.1V190.9C512 232.4 494.8 272.1 464.4 300.4L283.7 469.1C276.2 476.1 266.3 480 256 480C245.7 480 235.8 476.1 228.3 469.1L47.59 300.4C17.23 272.1 .0003 232.4 .0003 190.9L0 190.9z'/%3E%3C/svg%3E");
        background-size: contain;
        opacity: 0.2;
    }
    
    .birthday-countdown {
        font-size: 1.2rem;
        font-weight: 600;
        margin-top: 10px;
    }
    
    .birthday-badge {
        background: white;
        color: var(--birthday-primary);
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .birthday-icon {
        margin-right: 8px;
        color: var(--birthday-primary);
    }
    
    .profile-section {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .section-title {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f2f5;
    }
    
    @media (max-width: 768px) {
        .profile-picture {
            width: 120px;
            height: 120px;
        }
        
        .birthday-card {
            padding: 15px;
        }
    }
    
    /* Animation for birthday elements */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .birthday-pulse {
        animation: pulse 2s infinite;
    }
</style>

<div class="container-fluid py-4">
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <div class="profile-picture-container">
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
                <h2 class="mb-1">{{ auth()->user()->name }}</h2>
                <p class="text-lg text-muted mb-2">
                    <i class="fas fa-briefcase me-2"></i>{{ auth()->user()->position }} - {{ auth()->user()->department->name ?? 'No Department' }}
                </p>
                <p class="mb-3">
                    <i class="fas fa-envelope me-2"></i>{{ auth()->user()->email }}
                    <span class="mx-3">|</span>
                    <i class="fas fa-phone me-2"></i>{{ auth()->user()->phone ?? 'N/A' }}
                </p>
                <span class="badge bg-gradient-{{ auth()->user()->status === 'active' ? 'success' : 'danger' }} px-3 py-2">
                    {{ ucfirst(auth()->user()->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            @if(auth()->user()->birth_date)
                <div class="birthday-card birthday-pulse">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="text-white mb-3">
                                <i class="fas fa-birthday-cake me-2"></i> My Birthday
                            </h5>
                            <div class="birthday-badge">
                                <i class="fas fa-calendar-day birthday-icon"></i>
                                {{ auth()->user()->birth_date->format('F j') }}
                            </div>
                            <div class="birthday-countdown text-white mt-3">
                                @if($upcomingBirthday['days_remaining'] == 0)
                                    <i class="fas fa-gift me-2"></i> It's my birthday today!
                                @else
                                    <i class="fas fa-calendar-check me-2"></i> 
                                    {{ $upcomingBirthday['days_remaining'] }} days until my {{ $upcomingBirthday['age'] }}th birthday
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('birthdays.my') }}" class="btn btn-sm btn-light rounded-pill">
                            <i class="fas fa-arrow-right me-1"></i> View
                        </a>
                    </div>
                </div>
            @endif

            <div class="profile-section mt-4">
                <h5 class="section-title">
                    <i class="fas fa-info-circle me-2"></i>Basic Information
                </h5>
                <div class="info-item mb-3">
                    <div class="text-muted small">Employee ID</div>
                    <div class="fw-bold">{{ auth()->user()->employee_id ?? 'N/A' }}</div>
                </div>
                <div class="info-item mb-3">
                    <div class="text-muted small">Hire Date</div>
                    <div class="fw-bold">{{ auth()->user()->hire_date ? auth()->user()->hire_date->format('M d, Y') : 'N/A' }}</div>
                </div>
                <div class="info-item mb-3">
                    <div class="text-muted small">Tenure</div>
                    <div class="fw-bold">{{ auth()->user()->hire_date ? now()->diffInYears(auth()->user()->hire_date) . ' years' : 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="text-muted small">Leave Balance</div>
                    <div class="fw-bold">{{ auth()->user()->leave_balance ?? 0 }} days remaining</div>
                </div>
            </div>

            <div class="profile-section">
                <h5 class="section-title">
                    <i class="fas fa-id-card me-2"></i>Contact Details
                </h5>
                <div class="info-item mb-3">
                    <div class="text-muted small">Address</div>
                    <div class="fw-bold">{{ auth()->user()->address ?? 'Not specified' }}</div>
                </div>
                <div class="info-item mb-3">
                    <div class="text-muted small">Emergency Contact</div>
                    <div class="fw-bold">{{ auth()->user()->emergency_contact ?? 'Not specified' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="profile-section">
                <h5 class="section-title">
                    <i class="fas fa-user-edit me-2"></i>Edit Profile
                </h5>
                <form id="profileForm" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name', auth()->user()->name) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="{{ old('phone', auth()->user()->phone) }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label">Birth Date</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-birthday-cake"></i>
                                    </span>
                                    <input type="date" name="birth_date" class="form-control" 
                                           value="{{ old('birth_date', auth()->user()->birth_date ? auth()->user()->birth_date->format('Y-m-d') : '') }}">
                                </div>
                                <small class="text-muted">Your birthday will be visible to colleagues</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" 
                                       value="{{ old('address', auth()->user()->address) }}">
                            </div>
                        </div>
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

            <div class="profile-section">
                <h5 class="section-title">
                    <i class="fas fa-lock me-2"></i>Security Settings
                </h5>
                <a href="{{ route('password.change') }}" class="btn btn-warning px-4">
                    <i class="fas fa-key me-2"></i> Change Password
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Profile picture upload with preview
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
    
    // Form submission with AJAX
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
        submitBtn.disabled = true;
        
        fetch(this.action, {
            method: this.method,
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    toastr.error(data.errors[field][0]);
                });
            } else if (data.success) {
                toastr.success(data.message);
                
                if (data.birth_date) {
                    const birthDate = new Date(data.birth_date);
                    const options = { month: 'long', day: 'numeric' };
                    const formattedDate = birthDate.toLocaleDateString('en-US', options);
                    
                    if (document.querySelector('.birthday-badge')) {
                        document.querySelector('.birthday-badge').innerHTML = 
                            `<i class="fas fa-calendar-day birthday-icon"></i>${formattedDate}`;
                    }
                    
                    if (document.querySelector('.birthday-countdown')) {
                        const nextBirthday = new Date(birthDate);
                        nextBirthday.setFullYear(new Date().getFullYear());
                        if (nextBirthday < new Date()) {
                            nextBirthday.setFullYear(new Date().getFullYear() + 1);
                        }
                        
                        const daysRemaining = Math.ceil((nextBirthday - new Date()) / (1000 * 60 * 60 * 24));
                        const age = nextBirthday.getFullYear() - birthDate.getFullYear();
                        
                        document.querySelector('.birthday-countdown').innerHTML = 
                            `<i class="fas fa-calendar-check me-2"></i>${daysRemaining} days until my ${age}th birthday`;
                    }
                }
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred while saving');
        })
        .finally(() => {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });
</script>

@endsection