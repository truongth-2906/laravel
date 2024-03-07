<div class="form-group row">
    <label for="roles" class="col-md-2 col-form-label">@lang('Roles')</label>

    <div class="col-md-10">
        <div x-show="userType === '{{ $model::TYPE_ADMIN }}'">
            @include('backend.auth.includes.partials.role-type', ['type' => $model::TYPE_ADMIN])
        </div>

        <div x-show="userType === '{{ $model::TYPE_FREELANCER }}'">
            @include('backend.auth.includes.partials.role-type', ['type' => $model::TYPE_FREELANCER])
        </div>

        <div x-show="userType === '{{ $model::TYPE_EMPLOYER }}'">
            @include('backend.auth.includes.partials.role-type', ['type' => $model::TYPE_EMPLOYER])
        </div>
    </div>
</div><!--form-group-->
