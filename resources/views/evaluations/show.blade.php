@extends('dash.dash')

@section('contentdash')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Evaluation Details</h4>
            <a href="{{ route('evaluations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Employee Information</h5>
                    <p><strong>Name:</strong> {{ $evaluation->user->name }}</p>
                    <p><strong>Email:</strong> {{ $evaluation->user->email }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Evaluation Details</h5>
                    <p><strong>Date:</strong> {{ $evaluation->evaluation_date->format('Y-m-d') }}</p>
                    <p><strong>Overall Score:</strong> 
                        <span class="badge badge-pill badge-{{ $evaluation->average >= 7 ? 'success' : ($evaluation->average >= 5 ? 'warning' : 'danger') }}">
                            {{ number_format($evaluation->average, 1) }}/10
                        </span>
                    </p>
                </div>
            </div>

            <div class="evaluation-scores">
                <h5 class="mb-3">Evaluation Criteria</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h6>Punctuality</h6>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $evaluation->punctuality * 10 }}%"
                                 aria-valuenow="{{ $evaluation->punctuality }}"
                                 aria-valuemin="1"
                                 aria-valuemax="10">
                                {{ $evaluation->punctuality }}/10
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6>Work Quality</h6>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" 
                                 style="width: {{ $evaluation->work_quality * 10 }}%"
                                 aria-valuenow="{{ $evaluation->work_quality }}"
                                 aria-valuemin="1"
                                 aria-valuemax="10">
                                {{ $evaluation->work_quality }}/10
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h6>Teamwork</h6>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: {{ $evaluation->teamwork * 10 }}%"
                                 aria-valuenow="{{ $evaluation->teamwork }}"
                                 aria-valuemin="1"
                                 aria-valuemax="10">
                                {{ $evaluation->teamwork }}/10
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($evaluation->notes)
            <div class="notes-section mt-4">
                <h5>Additional Notes</h5>
                <div class="notes-content p-3 bg-light rounded">
                    {{ $evaluation->notes }}
                </div>
            </div>
            @endif
        </div>
        
        @can('update', $evaluation)
        <div class="card-footer text-right">
            <a href="{{ route('evaluations.edit', $evaluation->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Evaluation
            </a>
        </div>
        @endcan
    </div>
</div>

<style>
    .progress {
        background-color: #f1f1f1;
        border-radius: 4px;
    }
    .progress-bar {
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .notes-content {
        white-space: pre-line;
    }
    .badge {
        font-size: 14px;
        padding: 5px 10px;
    }
</style>
@endsection