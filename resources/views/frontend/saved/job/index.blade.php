@extends('frontend.saved.index', [
    'type' => SAVED_JOB,
    'filterModalId' => '#filter-job-modal',
    'parentSelector' => 'job-saved',
])

@section('table')
    <div id="table-wrapper">
        @include('frontend.saved.job.table')
    </div>
    @include('frontend.saved.job.filter-modal')
@endsection
