@extends('layouts.login')

@section('content')
<div class="login-container">

            <div class="row">

                <div class="col-sm-6">

                    <!-- Errors container -->
                    <div class="errors-container">
                    </div>
                    <!-- Add class "fade-in-effect" for login form effect -->
                    <form method="post" role="form" id="login" class="login-form fade-in-effect" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        <div class="login-header">
                            <a href="javascript:{};" class="logo" style="text-align: center;">
                                <img src="{{ URL::to('assets/images/pureit.jpg') }}" alt="" width="250" />
                            </a>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Username</label>
                            <input type="text" class="form-control" name="email" id="email" value="{{ old('email') }}" autocomplete="off" />
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password" autocomplete="off" />
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary  btn-block text-left">
                                <i class="fa-lock"></i>
                                Log In
                            </button>
                        </div>
                        <div class="login-footer">
<!--                            <a href="{{ route('password.request') }}">Forgot your password?</a>-->
                            {{--<div class="info-links">--}}
                                {{--<a href="javascript:{}">ToS</a> ---}}
                                {{--<a href="javascript:{}">Privacy Policy</a>--}}
                            {{--</div>--}}
                        </div>
                    </form>
                    <script type="text/javascript">
                            // Reveal Login form
                            setTimeout(function(){ $(".fade-in-effect").addClass('in'); }, 1);
                            // Validation and Ajax action
                            // Set Form focus
                            $("form#login .form-group:has(.form-control):first .form-control").focus();
                    </script>
                </div>
            </div>
        </div>
@endsection
