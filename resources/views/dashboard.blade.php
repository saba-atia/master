@extends('dash.dash')

@section('contentdash')
<div class="container">
    <h3 class="mb-4">My Performance Evaluation</h3>

    @if($evaluation)
    <div class="row">
        <!-- Progress Bars -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Evaluation Scores</h5>
                </div>
                <div class="card-body">
                    @foreach(['punctuality', 'work_quality', 'teamwork'] as $metric)
                    <div class="mb-3">
                        <label>{{ ucfirst(str_replace('_', ' ', $metric)) }}</label>
                        <div class="progress" style="height: 25px;">
                            @php
                                $score = $evaluation->{$metric} * 10;
                                $color = match(true) {
                                    $score >= 80 => 'bg-success',
                                    $score >= 50 => 'bg-warning',
                                    default => 'bg-danger'
                                };
                            @endphp
                            <div class="progress-bar {{ $color }}" 
                                 style="width: {{ $score }}%"
                                 role="progressbar">
                                {{ $evaluation->{$metric} }}/10
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Performance Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info">
        No evaluation available yet.
    </div>
    @endif
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($evaluation)
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Punctuality', 'Work Quality', 'Teamwork'],
                datasets: [{
                    label: 'My Scores',
                    data: [
                        {{ $evaluation->punctuality }},
                        {{ $evaluation->work_quality }},
                        {{ $evaluation->teamwork }}
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointRadius: 5
                }, {
                    label: 'Company Average',
                    data: [7, 7, 7], // يمكن استبدالها بمتوسط الشركة
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    pointRadius: 5
                }]
            },
            options: {
                scales: {
                    r: {
                        angleLines: { display: true },
                        suggestedMin: 0,
                        suggestedMax: 10
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection
@endsection