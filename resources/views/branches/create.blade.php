@extends('dash.dash')

@section('contentdash')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            <i class="material-icons opacity-10">business</i>
                            Add New Branch
                        </h6>
                    </div>
                </div>
                <div class="card-body px-4 pb-2">
                    <form action="{{ route('branches.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3 focused is-focused">
                                    <label class="form-label">Branch Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" required>
                                </div>
                            </div>
                        </div>
               
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="material-icons opacity-10 me-1"></i>
                                Save Branch
                            </button>
                            <a href="{{ route('branches.index') }}" class="btn btn-secondary ms-2">
                                <i class="material-icons opacity-10 me-1"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection