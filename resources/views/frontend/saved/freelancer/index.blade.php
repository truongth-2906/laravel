@extends('frontend.saved.index', [
    'type' => SAVED_FREELANCER,
    'filterModalId' => '#employer-filter-freelancer-modal',
    'parentSelector' => 'freelancer-saved',
])

@section('table')
    <div id="table-wrapper">
        @include('frontend.saved.freelancer.table')
    </div>
    @include('frontend.saved.freelancer.filter-modal')
@endsection
