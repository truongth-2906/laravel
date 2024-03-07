@extends('frontend.layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <div class="container py-4 full-height">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if($logged_in_user->isEmployer())
                    @include('frontend.employer.index')
                @else
                    @include('frontend.freelancer.index')
                @endif
            </div><!--col-md-10-->
        </div><!--row-->
    </div><!--container-->
@endsection
