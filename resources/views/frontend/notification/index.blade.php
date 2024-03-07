@extends('frontend.layouts.app')

@section('title', __('Notifications'))

@section('content')
    <div class="p-32">
        <div class="general-card">
            <div class="w-100 general-card__header d-flex flex-wrap">
                <div class="d-flex flex-column align-items-start flex-grow-1">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="color-000000 font-18 font-weight-600 mr-2">@lang('Your Notifications')</div>
                        <div
                            class="color-2200A5 font-12 d-flex justify-content-center align-items-center font-weight-500 tag-pill tag-pill-primary">
                            <span class="mr-1" id="total-number-unread-notification">{{ $numberUnreadNotification }}</span> @lang('New Notifications')
                        </div>
                    </div>
                    <div class="color-000000 font-14">@lang('Job updates, payment updates and messages for you.')</div>
                </div>
            </div>
            <div class="w-100 general-card__body" id="table-wrapper">
                @include('frontend.notification.table')
        </div>
    </div>
    @include('backend.includes.modal_confirm_delete', ['type' => TYPE_DELETE_NOTIFICATION])

    <form action="#" method="post" id="form-delete" hidden>
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('before-scripts')
    <script>
        const IS_NOTIFICATIONS_SCREEN = true;
    </script>
@endpush
