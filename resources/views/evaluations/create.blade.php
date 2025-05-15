{{-- resources/views/evaluations/create.blade.php --}}
@extends('dash.dash')

@section('contentdash')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Add New Evaluation</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('evaluations.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="user_id" class="col-md-4 col-form-label text-md-right">Employee</label>
                            <div class="col-md-6">
                                <select id="user_id" class="form-control @error('user_id') is-invalid @enderror" name="user_id" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="evaluation_date" class="col-md-4 col-form-label text-md-right">Evaluation Date</label>
                            <div class="col-md-6">
                                <input id="evaluation_date" type="date" class="form-control @error('evaluation_date') is-invalid @enderror" 
                                    name="evaluation_date" value="{{ old('evaluation_date', date('Y-m-d')) }}" required>
                                @error('evaluation_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="punctuality" class="col-md-4 col-form-label text-md-right">Punctuality (1-10)</label>
                            <div class="col-md-6">
                                <input id="punctuality" type="number" min="1" max="10" 
                                    class="form-control @error('punctuality') is-invalid @enderror" 
                                    name="punctuality" value="{{ old('punctuality') }}" required>
                                @error('punctuality')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="work_quality" class="col-md-4 col-form-label text-md-right">Work Quality (1-10)</label>
                            <div class="col-md-6">
                                <input id="work_quality" type="number" min="1" max="10" 
                                    class="form-control @error('work_quality') is-invalid @enderror" 
                                    name="work_quality" value="{{ old('work_quality') }}" required>
                                @error('work_quality')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="teamwork" class="col-md-4 col-form-label text-md-right">Teamwork (1-10)</label>
                            <div class="col-md-6">
                                <input id="teamwork" type="number" min="1" max="10" 
                                    class="form-control @error('teamwork') is-invalid @enderror" 
                                    name="teamwork" value="{{ old('teamwork') }}" required>
                                @error('teamwork')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="notes" class="col-md-4 col-form-label text-md-right">Notes</label>
                            <div class="col-md-6">
                                <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" 
                                    name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Evaluation
                                </button>
                                <a href="{{ route('evaluations.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection