<div class="evaluation-chart">
    @if($evaluation)
        <h5>Evaluation Score: {{ $evaluation->score }}/100</h5>
        <div class="progress">
            <div class="progress-bar" 
                 role="progressbar" 
                 style="width: {{ $evaluation->score }}%" 
                 aria-valuenow="{{ $evaluation->score }}" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
            </div>
        </div>
        <p class="mt-2">Date: {{ $evaluation->created_at->format('M d, Y') }}</p>
    @else
        <p>No evaluation data available</p>
    @endif
</div>