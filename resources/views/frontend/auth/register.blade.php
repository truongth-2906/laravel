@extends('frontend.index')

@section('title', __('Register'))

@section('content')
    <div class="min-vh-100 login-container">
        <div class="row justify-content-center m-auto w-desktop">
            <div class="col-md-12 mt-4 text-center logo">
                <img src="img/logo.svg" alt="Logo">
            </div>
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('frontend.auth.register')}}" method="POST" autocomplete="off" id="sign-up-form">
                            @csrf
                            <div class="form-group row text-center">
                                <div class="col-md-12">
                                    <h2 class="login-title">@lang('Create your Account')</h2>
                                </div>
                                <div class="col-md-12">
                                    <p class="login-content">@lang('Please fill out the form below to create your account.')</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12 mb-2">
                                    <label for="" class="mb-0 text-14">
                                        @lang('I am a(n)')
                                    </label>
                                    <div class="form-content text-color font-size-16 w-100">
                                        <select id="" class="form-control" name="type">
                                            <option value="">@lang("Please select an option")</option>
                                            <option
                                                value="{{ TYPE_FREELANCER }}" {{ old('type') == TYPE_FREELANCER ? 'selected' : ''}}>
                                                @lang("Freelancer")
                                            </option>
                                            <option
                                                value="{{ TYPE_EMPLOYER }}" {{ old('type') == TYPE_EMPLOYER ? 'selected' : ''}}>
                                                @lang("Employer")
                                            </option>
                                        </select>
                                        @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <label for="" class="mb-0 text-14">
                                        @lang('First Name')
                                    </label>
                                    <input type="text" name="firstname" class="form-control"
                                           placeholder="{{ __('ex. John') }}" value="{{ old('firstname') }}"
                                           maxlength="255" required autofocus/>
                                    @error('firstname')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label for="" class="mb-0 text-14">
                                        @lang('Last Name')
                                    </label>
                                    <input type="text" name="lastname" class="form-control"
                                           placeholder="{{ __('ex. Doe') }}" value="{{ old('lastname') }}"
                                           maxlength="255" required autofocus/>
                                    @error('lastname')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-2">
                                    <label for="email" class="mb-0 text-14">
                                        @lang('Email')
                                    </label>
                                    <input type="email" name="email" class="form-control"
                                           placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}"
                                           maxlength="255" required autofocus autocomplete="email"/>
                                    @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="col-md-12 mb-2">
                                    <label for="phone_number" class="mb-0 text-14">
                                        @lang('Phone Number')
                                    </label>
                                    <div class="d-flex">
                                        @include('backend.includes.partials.input-phone-number', [
                                            'parent_class' => 'register-form',
                                            'placeholder' => __('Enter your phone number')
                                        ])
                                    </div>
                                    @error('calling_code')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    @error('phone_number')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-2">
                                    <label for="" class="mb-0 text-14">
                                        @lang('Password')
                                    </label>
                                    <input type="password" name="password" class="form-control"
                                           placeholder="{{ __('Enter your password') }}" maxlength="100" required
                                           autocomplete="current-password"/>
                                    @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="password" class="mb-0 text-14">
                                        @lang('Confirm Password')
                                    </label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                           placeholder="{{ __('Enter your password confirmation') }}" maxlength="100"
                                           required
                                           autocomplete="current-password"/>
                                    @error('password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <div class="form-check remember py-2 text-remember">
                                        <input name="agree_terms_of_use" id="agree-terms-of-use" class="form-check-input checkbox-login cursor-pointer"
                                               type="checkbox" {{ old('agree_terms_of_use') ? 'checked' : '' }} />
                                        <label class="form-check-label text-14 ml-2 cursor-pointer" for="agree-terms-of-use">
                                            @lang('I agree to the ')
                                            <a href="https://www.escrow.com/escrow-101/terms-of-use" target="_blank">@lang('terms of use')</a>,
                                            <a href="https://www.escrow.com/escrow-101/general-escrow-instructions" target="_blank">@lang('general Escrow instruction')</a>
                                            @lang('and')
                                            <a href="https://www.escrow.com/escrow-101/privacy-policy" target="_blank">@lang('privacy policy.')</a>
                                        </label>
                                        @error('agree_terms_of_use')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div><!--form-group-->

                            @if(config('base.access.captcha.registration'))
                                <div class="row">
                                    <div class="col">
                                        @captcha
                                        <input type="hidden" name="captcha_status" value="true"/>
                                    </div><!--col-->
                                </div><!--row-->
                            @endif

                            <div class="form-group row mb-0">
                                <div class="d-flex w-100 button-login">
                                    <button class="btn btn-primary w-100 mb-2 login-button" id="btn-sign-up" {{ !old() || (old() && !old('agree_terms_of_use')) ? 'disabled' : '' }}
                                            type="submit">@lang('Create Your Account')
                                        <img class="ml-2" src="{{asset('img/icon-sideways.svg')}}" alt="">
                                    </button>
                                </div>
                            </div><!--form-group-->
                        </form>
                    </div>
                </div>
            </div><!--col-md-8-->
            <div class="d-flex mt-3 justify-content-center align-items-center">
                <p class="no-account">@lang('Already have an account?')&ensp;</p>
                <p>
                    <a class="signup" href="{{ route('frontend.auth.login') }}">@lang('Sign in')</a>
                </p>
            </div>
        </div><!--row-->
    </div>
@endsection
