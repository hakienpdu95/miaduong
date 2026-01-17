@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <h2 class="h4 fw-bold mb-4 text-center">Đăng nhập</h2>

                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        {{ $errors->first('identifier') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="identifier" class="form-label">Email hoặc Tên đăng nhập</label>
                        <input name="identifier" type="text" id="identifier" class="form-control" placeholder="Nhập email hoặc tên đăng nhập" autocomplete="off" value="{{ old('identifier') }}" />
                        @error('identifier')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input name="password" type="password" id="password" class="form-control" placeholder="Nhập mật khẩu" autocomplete="off" />
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <x-turnstile name="turnstileResponse" data-theme="light" data-action="login" data-appearance="non-interactive" />
                        @error('turnstileResponse')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div class="form-check">
                            <input name="remember" type="checkbox" class="form-check-input" id="remember" {{ old('remember') ? 'checked' : '' }} />
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection