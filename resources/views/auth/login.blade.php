
@extends('layouts.minimal')

@section('content')
    @if ($errors->any())
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#fbc02d',
                });
            });
        </script>
        @endpush
    @endif
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="auth-container">
                <h2>Login</h2>

                @if (session('success'))
                    @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: @json(session('success')),
                                confirmButtonColor: '#3085d6',
                            });
                        });
                    </script>
                    @endpush
                @endif

                @if (session('error'))
                    @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Login Failed',
                                text: @json(session('error')),
                                confirmButtonColor: '#d32f2f',
                            });
                        });
                    </script>
                    @endpush
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate autocomplete="off">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" value="{{ old('email') }}" autofocus class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <span class="invalid-feedback" style="display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mt-3">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                    </div>

                    <div class="form-group mt-3">
                        <label>
                            <input type="checkbox" name="remember" value="1">
                            Remember Me
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        Login
                    </button>

                    <div class="mt-3">
                        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                    </div>
                </form>

                <p class="mt-3">
                    Don't have an account?
                    <a href="{{ route('register') }}">Register</a>
                </p>

            </div>

        </div>
    </div>
</div>
@endsection