@extends('dash.dash')
@section('title', 'birthdays')
@section('contentdash')


<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .employee-section {
        background-color: #f0f8ff;
        text-align: center;
        padding: 30px;
    }
    .admin-section {
        background-color: #fff0f5;
        text-align: center;
        padding: 30px;
    }
    .super-admin-section {
        background-color: #f0fff0;
        text-align: center;
        padding: 30px;
    }
    .alert-section {
        background-color: #fff8dc;
        text-align: center;
        padding: 30px;
    }
    h1 {
        color: #2c3e50;
    }
    .employee-title {
        color: #3498db;
    }
    .admin-title {
        color: #e74c3c;
    }
    .super-admin-title {
        color: #27ae60;
    }
    p {
        color: #7f8c8d;
        font-size: 16px;
        line-height: 1.6;
    }
</style>
</head>
<body>
<div class="container">
    @if(auth()->user()->role === 'employee')
        <div class="employee-section">
            <h1 class="employee-title">🎉 Happy Birthday Wishes</h1>
            <p>Wishing you a fantastic birthday filled with joy and happiness! May this year bring you success and fulfillment in all your endeavors.</p>
        </div>
    @elseif(auth()->user()->role === 'admin')
        <div class="admin-section">
            <h1 class="admin-title">Birthday Management - HR Panel</h1>
            <p>As HR admin, you can track and organize employee birthdays. Use this panel to schedule celebrations, send greetings, and maintain employee engagement.</p>
        </div>
    @elseif(auth()->user()->role === 'super_admin')
        <div class="super-admin-section">
            <h1 class="super-admin-title">Birthdays Overview - Management</h1>
            <p>Management dashboard for monitoring employee well-being through birthday celebrations. This overview helps ensure company culture and employee satisfaction metrics are maintained.</p>
        </div>
    @else
        <div class="alert-section">
            <h1>⚠️ Access Restricted</h1>
            <p>Your user role does not have permissions to view this content. Please contact system administrator if you believe this is an error.</p>
        </div>
    @endif
</div>

@endsection