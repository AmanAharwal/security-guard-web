@extends('layouts.app')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Create a leave encashment</h4>

                        <div class="page-title-right">
                            <a href="{{ route('employee-leave-encashment.index') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Back to leave encashment</a>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('employee-leave-encashment.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @include('admin.employee-leave-encashment.form')
                            </form>    
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection