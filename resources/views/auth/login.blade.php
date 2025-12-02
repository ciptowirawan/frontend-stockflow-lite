@extends('layouts.form')

@section('content')
<section class="landing-page">
    <div class="card form-content rounded-5">
        <div class="card-body">
            <div class="container my-3">
                <div class="row mb-2">
                    <div class="text-start mb-0">Welcome Back</div>
                    <span class="text-muted mt-0">Hello there, sign in to continue!</span>
                </div>
                <form method="POST" action="/login">
                    @csrf
                    <div class="row mb-4">
                        <label for="email" class="col-md-4 mb-2 text-muted">{{ __('Email Address') }}</label>
    
                        <div class="col-md-12">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email">
    
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
    
                    <div class="row mb-3">
                        <label for="password" class="col-md-4 mb-2 text-muted">{{ __('Password') }}</label>
    
                        <div class="col-md-12">
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter your password"
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password h-100" data-target="password">
                                        <i class="fa-solid fa-eye"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                        
                    <div class="col-md-12 mt-4">
                        <button type="submit" class="btn button-form">
                            Sign in
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    
</section>

<footer class="p-2 bg-light border-white" style="border: 0;">
    <div class="my-auto">
        <div class="copyright text-center text-muted my-auto">
            <span>Copyright &copy; Cipto Wirawan 2025</span>
        </div>
    </div>
</footer>

@endsection

@push('body-scripts')
    <script src="{{ asset('js/password/form.js') }}"></script>
@endpush