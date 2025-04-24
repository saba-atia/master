@extends('dash.dash')
@section('title', 'Finance')
@section('contentdash')
<style>
    body {
        background-color: #f8f9fa;
    }
    .finance-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        border: none;
    }
    .employee-card {
        border-left: 4px solid #4e73df;
    }
    .admin-card {
        border-left: 4px solid #1cc88a;
    }
    .super-admin-card {
        border-left: 4px solid #f6c23e;
    }
    .unauthorized-card {
        border-left: 4px solid #e74a3b;
    }
    .card-title {
        font-weight: 600;
        margin-bottom: 20px;
    }
    .finance-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
    }
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    .chart-container {
        height: 300px;
        position: relative;
        margin-bottom: 30px;
    }
    .summary-card {
        transition: transform 0.3s;
    }
    .summary-card:hover {
        transform: translateY(-5px);
    }
</style>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @if(auth()->user()->role === 'employee')
                <div class="card finance-card employee-card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="finance-icon">💰</div>
                            <h2 class="card-title text-primary">My Salary Details</h2>
                            <p class="text-muted">View your monthly salary details, deductions, and any raises if applicable.</p>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Month</th>
                                        <th>Base Salary</th>
                                        <th>Deductions</th>
                                        <th>Net Salary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>January 2023</td>
                                        <td>$5,000</td>
                                        <td>$200</td>
                                        <td>$4,800</td>
                                    </tr>
                                    <tr>
                                        <td>December 2022</td>
                                        <td>$5,000</td>
                                        <td>$150</td>
                                        <td>$4,850</td>
                                    </tr>
                                    <tr>
                                        <td>November 2022</td>
                                        <td>$5,000</td>
                                        <td>$300</td>
                                        <td>$4,700</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->role === 'admin')
                <div class="card finance-card admin-card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="finance-icon">💼</div>
                            <h2 class="card-title text-success">HR Finance Dashboard</h2>
                            <p class="text-muted">Monitor employee salaries, financial transactions, and monthly HR-related reports.</p>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Salary</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>John Smith</td>
                                        <td>Marketing</td>
                                        <td>$4,500</td>
                                        <td><span class="badge bg-success">Paid</span></td>
                                        <td><button class="btn btn-sm btn-outline-primary">Details</button></td>
                                    </tr>
                                    <tr>
                                        <td>Sarah Johnson</td>
                                        <td>Development</td>
                                        <td>$5,200</td>
                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                        <td><button class="btn btn-sm btn-outline-primary">Details</button></td>
                                    </tr>
                                    <tr>
                                        <td>Michael Brown</td>
                                        <td>Sales</td>
                                        <td>$4,800</td>
                                        <td><span class="badge bg-danger">Overdue</span></td>
                                        <td><button class="btn btn-sm btn-outline-primary">Details</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-end">
                            <button class="btn btn-success me-2">Process Payments</button>
                            <button class="btn btn-outline-secondary">Generate Reports</button>
                        </div>
                    </div>
                </div>

            @elseif(auth()->user()->role === 'super_admin')
                <div class="card finance-card super-admin-card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="finance-icon">📊</div>
                            <h2 class="card-title text-warning">Financial Overview - Management</h2>
                            <p class="text-muted">Comprehensive analysis of company budget, salaries, and annual expenses.</p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="card summary-card h-100">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Monthly Salaries</h5>
                                        <h3 class="text-primary">$125,000</h3>
                                        <p class="text-muted">+2.5% from last month</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card summary-card h-100">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Admin Expenses</h5>
                                        <h3 class="text-info">$42,300</h3>
                                        <p class="text-muted">-1.2% from last month</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card summary-card h-100">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Projected Profit</h5>
                                        <h3 class="text-success">$87,500</h3>
                                        <p class="text-muted">+5.8% from last month</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="chart-container">
                            <canvas id="financeChart"></canvas>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-warning me-2">Export Data</button>
                            <button class="btn btn-dark">Strategic Analysis</button>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('financeChart').getContext('2d');
                        const financeChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                                datasets: [
                                    {
                                        label: 'Salaries',
                                        data: [120000, 122000, 121500, 123000, 124500, 125000],
                                        backgroundColor: 'rgba(78, 115, 223, 0.7)',
                                        borderColor: 'rgba(78, 115, 223, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Expenses',
                                        data: [45000, 43000, 44000, 42000, 42500, 42300],
                                        backgroundColor: 'rgba(30, 204, 138, 0.7)',
                                        borderColor: 'rgba(30, 204, 138, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Profit',
                                        data: [75000, 79000, 77500, 81000, 82000, 87500],
                                        backgroundColor: 'rgba(246, 194, 62, 0.7)',
                                        borderColor: 'rgba(246, 194, 62, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Monthly Financial Overview'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    });
                </script>

            @else
                <div class="card finance-card unauthorized-card">
                    <div class="card-body text-center py-5">
                        <div class="finance-icon">⚠️</div>
                        <h2 class="card-title text-danger">Unauthorized Access</h2>
                        <p class="text-muted">You don't have permission to view this content. Please contact your system administrator.</p>
                        <a href="/" class="btn btn-danger mt-3">Return to Home</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection