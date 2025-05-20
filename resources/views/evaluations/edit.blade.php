@extends('dash.dash')

@section('contentdash')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Evaluation</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('evaluations.update', $evaluation->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="user_id">Employee</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $evaluation->user_id == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} @if($user->department) - {{ $user->department->name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="evaluation_date">Evaluation Date</label>
                    <input type="date" name="evaluation_date" id="evaluation_date" 
                           class="form-control" value="{{ $evaluation->evaluation_date->format('Y-m-d') }}" required>
                </div>
                
                <!-- Evaluation Criteria -->
                <div class="form-group">
                    <label for="work_quality">Work Quality (1-10)</label>
                    <input type="number" name="work_quality" id="work_quality" 
                           class="form-control" min="1" max="10" 
                           value="{{ $evaluation->work_quality }}" required>
                </div>
                
                <div class="form-group">
                    <label for="teamwork">Teamwork (1-10)</label>
                    <input type="number" name="teamwork" id="teamwork" 
                           class="form-control" min="1" max="10" 
                           value="{{ $evaluation->teamwork }}" required>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ $evaluation->notes }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Evaluation</button>
                <a href="{{ route('evaluations.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection