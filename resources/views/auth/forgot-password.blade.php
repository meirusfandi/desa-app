<x-guest-layout>
    @php
        $appName = app_setting('app_name', config('app.name'));

        $appLogo = app_setting('app_logo');
        $logoSrc = $appLogo ? asset('storage/' . $appLogo) : asset('mazer/assets/compiled/svg/logo.svg');
    @endphp

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="row h-100">
        <div class="col-lg-5 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2">
                        <img src="{{ $logoSrc }}" alt="{{ $appName }}" style="height: 45px">
                        <span class="fw-bold">{{ $appName }}</span>
                    </a>
                </div>
                <h1 class="auth-title">Forgot Password</h1>
                <p class="auth-subtitle mb-5">Input your email and we will send you reset password link.</p>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="email" class="form-control form-control-xl @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="bx bx-radio-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Send Reset Password Link</button>
                </form>

                <div class="text-center mt-5 text-lg fs-4">
                    <p class="text-gray-600">Remember your account? <a href="{{ route('login') }}" class="font-bold">Sign
                            in</a>.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right">

            </div>
        </div>
    </div>
</x-guest-layout>
