@extends('frontend.index')

@section('title', __('Login'))

@section('content')
    <div class="min-vh-100 login-container">
        <div class="row justify-content-center m-auto w-desktop">
            <div class="col-md-12 text-center logo">
                <img src="img/logo.svg" alt="Logo" class="img-size-login">
            </div>
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('frontend.auth.login')}}" method="POST">
                            @csrf
                            <div class="form-group row text-center">
                                <div class="offset-md-1 col-md-10 offset-md-1">
                                    <h2 class="login-title">@lang('welcome')</h2>
                                </div>
                                <div class="col-md-12">
                                    <p class="login-content">@lang('Please enter your details to log in to your account.')</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12 mb-2">
                                    <label for="email" class="mb-0 text-14">
                                        @lang('Email')
                                    </label>
                                    <input type="email" name="email" id="email" class="form-control height-input-login"
                                           placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}"
                                           maxlength="255" required autofocus autocomplete="email"/>
                                </div>
                                <div class="col-md-12">
                                    <label for="password" class="mb-0 text-14">
                                        @lang('Password')
                                    </label>
                                    <input type="password" name="password" id="password"
                                           class="form-control height-input-login"
                                           placeholder="{{ __('Enter your password') }}" maxlength="100" required
                                           autocomplete="current-password"/>
                                </div>

                                <div class="col-md-12">
                                    @foreach($errors->all() as $error)
                                        <div class="text-danger">
                                            {{ $error }}
                                        </div>
                                    @endforeach
                                </div>
                            </div><!--form-group-->

                            <div class="form-group remember-forgot">
                                <div>
                                    <div class="form-check remember p-0 text-remember">
                                        <input name="remember" id="remember" class="form-check-input checkbox-login"
                                               type="checkbox" {{ old('remember') ? 'checked' : '' }} />
                                        <label class="form-check-label text-14" for="remember">
                                            @lang('Remember for 30 days')
                                        </label>
                                    </div><!--form-check-->
                                </div>
                                <div>
                                    <a href="{{route('frontend.auth.password.request')}}"
                                       class="forgot">@lang('Forgot Password')</a>
                                </div>
                            </div><!--form-group-->

                            @if(config('base.access.captcha.login'))
                                <div class="row">
                                    <div class="col">
                                        @captcha
                                        <input type="hidden" name="captcha_status" value="true"/>
                                    </div><!--col-->
                                </div><!--row-->
                            @endif

                            <div class="form-group row mb-0">
                                <div class="d-flex w-100 button-login">
                                    <button class="btn w-100 mb-2 login-button" type="submit"
                                            onclick="sessionStorage.removeItem('dont-show-again-freelancer');
                                            sessionStorage.removeItem('dont-show-again-employer');
                                            sessionStorage.removeItem('dont-show-again-job');
                                            ">@lang('SIGN IN')
                                        <img class="ml-2" src="{{asset('img/icon-sideways.svg')}}" alt="">
                                    </button>
                                </div>
                            </div><!--form-group-->
                        </form>
                    </div>
                </div>
            </div><!--col-md-8-->
            <div class="d-flex mt-3 justify-content-center align-items-center">
                <p class="no-account">@lang('Don’t have an account?')&ensp;</p>
                <p><a class="signup" href="{{route('frontend.auth.register')}}">@lang('Sign up')</a></p>
            </div>
        </div><!--row-->
    </div>
@endsection
