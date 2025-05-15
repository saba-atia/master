@extends('dash.dash')

@section('contentdash')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-white text-capitalize ps-3">
                                <i class="material-icons opacity-10 me-2">business</i>
                                Branches Management
                            </h6>
                            <a href="{{ route('branches.create') }}" class="btn btn-sm btn-light me-3">
                                <i class="material-icons opacity-10 me-1">add</i>
                                Add New Branch
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Branch</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($branches as $branch)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <i class="material-icons opacity-10">business</i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center ms-2">
                                                <h6 class="mb-0 text-sm">{{ $branch->name }} / {{ $branch->address }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                  
                                  
                                    <td class="align-middle">
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-sm btn-outline-info me-2">
                                                <i class="material-icons opacity-10">edit</i>
                                            </a>
                                            
                                            @if(Route::has('branches.show'))
                                            <a href="{{ route('branches.show', $branch->id) }}" class="btn btn-sm btn-outline-primary me-2" title="Branch Details">
                                                <i class="material-icons opacity-10">info</i>
                                            </a>
                                            @endif
                                            
                                            @if(Route::has('branches.employees'))
                                            <a href="{{ route('branches.employees', $branch->id) }}" class="btn btn-sm btn-outline-primary me-2" title="Branch Employees">
                                                <i class="material-icons opacity-10">people</i>
                                            </a>
                                            @endif
                                            
                                            @if(Route::has('branches.attendance'))
                                            <a href="{{ route('branches.attendance', $branch->id) }}" class="btn btn-sm btn-outline-primary me-2" title="Branch Attendance">
                                                <i class="material-icons opacity-10">schedule</i>
                                            </a>
                                            @endif

                                            <form action="{{ route('branches.destroy', $branch->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="material-icons opacity-10">delete</i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection