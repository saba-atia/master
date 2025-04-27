@extends('dash.dash')
@section('title', 'Change Password')

@section('contentdash')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Update Password</h5>
                </div>

                <div class="card-body">
                    <form id="passwordForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       name="new_password" 
                                       id="newPassword" 
                                       required
                                       placeholder="Enter new password">
                                <span class="input-group-text" onclick="togglePasswordVisibility('newPassword')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <small class="text-muted">
                                Must contain: uppercase, lowercase, number, and special character (@$!%*?&)
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control" 
                                       name="new_password_confirmation" 
                                       id="confirmPassword" 
                                       required
                                       placeholder="Confirm new password">
                                <span class="input-group-text" onclick="togglePasswordVisibility('confirmPassword')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery + SweetAlert2 + Font Awesome -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
    // تبديل عرض/إخفاء كلمة المرور
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        field.type = field.type === 'password' ? 'text' : 'password';
    }

    // معالجة إرسال النموذج
    $(document).ready(function() {
        $('#passwordForm').on('submit', function(e) {
            e.preventDefault();
            
            const newPassword = $('#newPassword').val();
            const confirmPassword = $('#confirmPassword').val();
            
            // التحقق من تطابق كلمتي المرور
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Passwords do not match!',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // التحقق من قوة كلمة المرور
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordRegex.test(newPassword)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Weak Password',
                    html: 'Password must contain:<ul><li>At least 8 characters</li><li>Uppercase letter</li><li>Lowercase letter</li><li>Number</li><li>Special character (@$!%*?&)</li></ul>',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }
            
            // إرسال البيانات عبر AJAX
            $.ajax({
                url: "{{ route('password.update') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            $('#passwordForm')[0].reset();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to update password',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
    });
</script>

<style>
    .input-group-text {
        cursor: pointer;
    }
    input[type="password"]::-ms-reveal {
        display: none;
    }
</style>
@endsection