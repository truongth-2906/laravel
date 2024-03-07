@extends('mails.app')

@section('content')
    <div class="color-344054 mt-5">
        <p>@lang('Dear ' . $user->name . ' ,')</p>
    </div>
    <div class="color-344054 mt-3">
        <p>@lang('A new job has been posted on Authomatorr.')</p>
    </div>
    <div class="color-344054" style="display: flex; flex-direction: wrap;">
        <div class="mr-2">@lang('Visit the following link to view job details:')</div>
        <a href="{{ $jobUrl }}">{{ $jobName }}</a>
    </div>
@endsection
