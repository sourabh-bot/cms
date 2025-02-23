@extends('layouts.master')
@section('title')
    Login
@endsection
@section('content')
    <div class="w-100 h-100">
        <div class="card w-50 m-auto my-5">
            <div class="card-header">
                <h4 class="text-center">User Login</h4>
            </div>
            <div class="card-body">
                <div class="container">
                    <x-alert />
                    <form action="{{ route('userLogin') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email"
                                class="form-control form-control-sm @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" />
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password"
                                class="form-control form-control-sm @error('password') is-invalid @enderror" />
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3 text-center">
                            <button class="btn btn-primary btn-sm">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
