@extends('frontend.layouts.app')

@section('title', __('Create Job'))

@section('content')
    <div class="wrapper">
        <form id="form-create-job" autocomplete="off" action="{{ route('frontend.employer.jobs.store') }}" class="job-form"
              method="post" enctype="multipart/form-data">
            @csrf
            @include('frontend.job.form')
        </form>
    </div>
@endsection
