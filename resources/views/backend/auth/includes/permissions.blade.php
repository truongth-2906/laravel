<div class="form-group row">
    <label for="permissions" class="col-md-2 col-form-label">@lang('Additional Permissions')</label>

    <div class="col-md-10">
        @include('backend.auth.role.includes.no-permissions-message')

        <div x-show="userType === '{{ $model::TYPE_ADMIN }}'">
            @include('backend.auth.includes.partials.permission-type', ['type' => $model::TYPE_ADMIN])
        </div>

        <div x-show="userType === '{{ $model::TYPE_FREELANCER}}'">
            @include('backend.auth.includes.partials.permission-type', ['type' => $model::TYPE_FREELANCER])
        </div>

        <div x-show="userType === '{{ $model::TYPE_EMPLOYER}}'">
            @include('backend.auth.includes.partials.permission-type', ['type' => $model::TYPE_EMPLOYER])
        </div>
    </div>
</div><!--form-group-->
