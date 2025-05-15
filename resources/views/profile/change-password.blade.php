@extends('dash.dash')

@section('contentdash')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0 text-dark font-weight-600">
                            <i class="fas fa-key mr-2 text-primary"></i>Change Password
                        </h3>
                        <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                    <hr class="mt-3 mb-0">
                </div>

                <div class="card-body px-4 py-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ session('success') }}</span>
                                <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
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

                    <form method="POST" action="{{ route('profile.password.update') }}" id="passwordForm" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="current_password" value="{{ auth()->user()->password }}">

                        <div class="form-group mb-4">
                            <label for="password" class="form-label font-weight-500">New Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-alternative">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                                </div>
                                <input id="password" type="password" 
                                       class="form-control border-0 bg-light @error('password') is-invalid @enderror" 
                                       name="password" required placeholder="Enter new password">
                                <div class="input-group-append">
                                    <button class="btn btn-light border-0 toggle-password" type="button" data-target="#password">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="password-requirements mt-3">
                                <small class="text-muted d-block mb-2">Password requirements:</small>
                                <ul class="list-unstyled mb-2 pl-3">
                                    <li class="req-char"><i class="fas fa-circle mr-2" style="font-size: 0.5rem;"></i> Minimum 8 characters</li>
                                    <li class="req-upper"><i class="fas fa-circle mr-2" style="font-size: 0.5rem;"></i> At least 1 uppercase letter</li>
                                    <li class="req-lower"><i class="fas fa-circle mr-2" style="font-size: 0.5rem;"></i> At least 1 lowercase letter</li>
                                    <li class="req-number"><i class="fas fa-circle mr-2" style="font-size: 0.5rem;"></i> At least 1 number</li>
                                    <li class="req-special"><i class="fas fa-circle mr-2" style="font-size: 0.5rem;"></i> At least 1 special character</li>
                                </ul>
                            </div>
                            <div class="password-strength mt-3">
                                <label class="d-block text-muted mb-1">Password strength:</label>
                                <div class="progress rounded-pill" style="height: 6px;">
                                    <div class="progress-bar" role="progressbar"></div>
                                </div>
                                <small class="strength-text text-muted mt-1"><span>Very Weak</span></small>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="password-confirm" class="form-label font-weight-500">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-alternative">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                                </div>
                                <input id="password-confirm" type="password" class="form-control border-0 bg-light" 
                                       name="password_confirmation" required placeholder="Confirm new password">
                                <div class="input-group-append">
                                    <button class="btn btn-light border-0 toggle-password" type="button" data-target="#password-confirm">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="password-match mt-2">
                                <small class="match-text"></small>
                            </div>
                        </div>

                        <div class="form-group mt-5 pt-3">
                            <button type="submit" class="btn btn-primary btn-block rounded-pill py-2 shadow-sm" id="submitBtn">
                                <i class="fas fa-key mr-2"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .card {
        border-radius: 12px;
        border: none;
    }
    
    .card-header {
        background: transparent;
    }
    
    .form-control {
        height: calc(1.8em + 0.75rem + 2px);
        font-size: 0.9rem;
    }
    
    .input-group-text {
        height: calc(1.8em + 0.75rem + 2px);
    }
    
    .password-strength .progress {
        background-color: #f1f4f7;
    }
    
    .password-strength .progress-bar {
        transition: width 0.3s ease, background-color 0.3s ease;
        border-radius: 10px;
    }
    
    .password-strength .strength-text span {
        font-weight: 500;
    }
    
    .password-match .match-text {
        font-weight: 500;
    }
    
    .req-char, .req-upper, .req-lower, .req-number, .req-special {
        color: #6c757d;
        transition: all 0.3s ease;
        position: relative;
        padding-left: 5px;
    }
    
    .req-char.valid, .req-upper.valid, .req-lower.valid, 
    .req-number.valid, .req-special.valid {
        color: #2dce89;
    }
    
    .req-char.valid i, .req-upper.valid i, .req-lower.valid i,
    .req-number.valid i, .req-special.valid i {
        color: #2dce89 !important;
    }
    
    .password-requirements ul li {
        margin-bottom: 0.3rem;
        line-height: 1.4;
    }
    
    .toggle-password {
        background: transparent !important;
        cursor: pointer;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .input-group-alternative {
        box-shadow: 0 1px 3px rgba(50, 50, 93, 0.1), 0 1px 0 rgba(0, 0, 0, 0.02);
        border-radius: 8px !important;
        transition: box-shadow 0.15s ease;
    }
    
    .input-group-alternative:hover {
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .form-control:focus {
        box-shadow: none;
    }

    /* Alert Styling */
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
    }

    .alert-success {
        border-left-color: #28a745;
    }

    .alert-danger {
        border-left-color: #dc3545;
    }

    .alert ul {
        margin-bottom: 0;
        padding-left: 1rem;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show success message from session
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK'
        });
    @endif

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(element) {
        element.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('data-target'));
            const icon = this.querySelector('i');
            
            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Password validation
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password-confirm');
    const progressBar = document.querySelector('.progress-bar');
    const strengthText = document.querySelector('.strength-text span');
    const matchText = document.querySelector('.match-text');
     const currentPasswordInput = document.getElementById('current_password');
    const currentPasswordError = document.querySelector('#current_password + .invalid-feedback');
    // Password requirements
    const reqChar = document.querySelector('.req-char');
    const reqUpper = document.querySelector('.req-upper');
    const reqLower = document.querySelector('.req-lower');
    const reqNumber = document.querySelector('.req-number');
    const reqSpecial = document.querySelector('.req-special');
    
     if (currentPasswordInput) {
        currentPasswordInput.addEventListener('input', function() {
            // إخفاء رسالة الخطأ عند البدء بالكتابة
            if (currentPasswordError) {
                currentPasswordError.style.display = 'none';
                this.classList.remove('is-invalid');
            }
        });
    }
    function checkPasswordStrength(password) {
        let strength = 0;
        
        // Length check
        if (password.length >= 8) {
            strength += 1;
            reqChar.classList.add('valid');
        } else {
            reqChar.classList.remove('valid');
        }
        
        // Uppercase check
        if (/[A-Z]/.test(password)) {
            strength += 1;
            reqUpper.classList.add('valid');
        } else {
            reqUpper.classList.remove('valid');
        }
        
        // Lowercase check
        if (/[a-z]/.test(password)) {
            strength += 1;
            reqLower.classList.add('valid');
        } else {
            reqLower.classList.remove('valid');
        }
        
        // Number check
        if (/[0-9]/.test(password)) {
            strength += 1;
            reqNumber.classList.add('valid');
        } else {
            reqNumber.classList.remove('valid');
        }
        
        // Special character check
        if (/[^A-Za-z0-9]/.test(password)) {
            strength += 1;
            reqSpecial.classList.add('valid');
        } else {
            reqSpecial.classList.remove('valid');
        }
        
        // Update UI
        const width = (strength / 5) * 100;
        progressBar.style.width = `${width}%`;
        
        // Set color and text based on strength
        if (strength <= 1) {
            progressBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Very Weak';
            strengthText.style.color = '#f5365c';
        } else if (strength <= 2) {
            progressBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Weak';
            strengthText.style.color = '#fb6340';
        } else if (strength <= 3) {
            progressBar.className = 'progress-bar bg-info';
            strengthText.textContent = 'Moderate';
            strengthText.style.color = '#11cdef';
        } else if (strength <= 4) {
            progressBar.className = 'progress-bar bg-primary';
            strengthText.textContent = 'Strong';
            strengthText.style.color = '#5e72e4';
        } else {
            progressBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Very Strong';
            strengthText.style.color = '#2dce89';
        }
    }
    
    function checkPasswordMatch() {
        if (passwordInput.value && confirmInput.value) {
            if (passwordInput.value === confirmInput.value) {
                matchText.textContent = 'Passwords match!';
                matchText.style.color = '#2dce89';
            } else {
                matchText.textContent = 'Passwords do not match!';
                matchText.style.color = '#f5365c';
            }
        } else {
            matchText.textContent = '';
        }
    }
    
    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });
    
    confirmInput.addEventListener('input', checkPasswordMatch);
});
</script>
@endsection