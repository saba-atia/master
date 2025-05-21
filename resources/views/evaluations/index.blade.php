@extends('dash.dash')
@section('title', 'Employee Evaluations')
@section('contentdash')
<div class="container">
    <h1 class="mb-4">Employee Evaluations</h1>

    @can('create', App\Models\Evaluation::class)
    <a href="{{ route('evaluations.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Add New Evaluation
    </a>
    @endcan

    <!-- Search and Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" id="search-input" class="form-control" placeholder="Search by employee name...">
                </div>
                <div class="col-md-5">
                    <input type="date" id="date-filter" class="form-control">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Evaluations Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Evaluation Records</h5>
        
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="evaluations-table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Punctuality</th>
                            <th>Work Quality</th>
                            <th>Teamwork</th>
                            <th>Average</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                        <tr>
<td>{{ $evaluation->user ? $evaluation->user->name : 'N/A' }}</td>                            <td>{{ $evaluation->evaluation_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $evaluation->punctuality * 10 }}%" 
                                         aria-valuenow="{{ $evaluation->punctuality }}" 
                                         aria-valuemin="1" 
                                         aria-valuemax="10">
                                        {{ $evaluation->punctuality }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: {{ $evaluation->work_quality * 10 }}%" 
                                         aria-valuenow="{{ $evaluation->work_quality }}" 
                                         aria-valuemin="1" 
                                         aria-valuemax="10">
                                        {{ $evaluation->work_quality }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ $evaluation->teamwork * 10 }}%" 
                                         aria-valuenow="{{ $evaluation->teamwork }}" 
                                         aria-valuemin="1" 
                                         aria-valuemax="10">
                                        {{ $evaluation->teamwork }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-pill bg-dark badge-{{ $evaluation->average >= 7 ? 'success' : ($evaluation->average >= 5 ? 'warning' : 'danger') }}">
                                    {{ number_format($evaluation->average, 1) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('evaluations.show', $evaluation->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('update', $evaluation)
                                <a href="{{ route('evaluations.edit', $evaluation->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete', $evaluation)
                                <form action="{{ route('evaluations.destroy', $evaluation->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $evaluations->links() }}
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Real-time search functionality
    document.getElementById('search-input').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        document.querySelectorAll('#evaluations-table tbody tr').forEach(row => {
            const employeeName = row.querySelector('td:first-child').textContent.toLowerCase();
            row.style.display = employeeName.includes(searchValue) ? '' : 'none';
        });
    });

    // Date filter functionality
    document.getElementById('date-filter').addEventListener('change', function() {
        const selectedDate = this.value;
        document.querySelectorAll('#evaluations-table tbody tr').forEach(row => {
            const rowDate = row.querySelector('td:nth-child(2)').textContent;
            row.style.display = (selectedDate === '' || rowDate === selectedDate) ? '' : 'none';
        });
    });

    // Email functionality
    function emailEvaluations() {
        alert('This would send the current filtered evaluations via email');
    }
</script>
@endsection
@endsection