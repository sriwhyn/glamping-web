@extends('layouts.app')

@section('title','Login')

@section('content')

<div class="container-fluid vh-100 d-flex justify-content-center align-items-center"
    style="background:linear-gradient(rgba(9,20,19,.60),rgba(9,20,19,.60)),url('{{ asset('images/login.jpeg') }}') center/cover no-repeat;">

    <div class="card border-0 rounded-4 shadow-lg p-4"
        style="width:420px;background:rgba(255,255,255,.95);backdrop-filter:blur(12px);box-shadow:0 18px 40px rgba(9,20,19,.25);">

        <div class="text-center mb-4">
            <img src="{{ asset('images/logo1.jpg') }}" class="rounded-circle shadow mb-3" style="width:90px;height:90px;object-fit:cover;">
            <h2 class="fw-bold mb-1" style="color:#243642;">LOGIN</h2>
            <p class="text-muted mb-0">Glamping Danau Kembar Scenery</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success rounded-3 border-0 shadow-sm p-3 mb-3" style="background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0!important;font-size:0.88rem;">
            <i class="bi bi-check-circle-fill me-1.5"></i>{{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger rounded-3 border-0 shadow-sm p-3 mb-3" style="background:#FEF2F2;color:#991B1B;border:1px solid #FCA5A5!important;font-size:0.88rem;">
            <i class="bi bi-exclamation-triangle-fill me-1.5"></i>{{ session('error') }}
        </div>
        @endif

        <form action="{{ route('login.proses') }}" method="POST">

            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>

                <div class="input-group">

                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-envelope-fill" style="color:#387478;"></i>
                    </span>

                    <input type="email"
                        name="email"
                        class="form-control border-start-0 py-2"
                        placeholder="Email Anda"
                        value="{{ request('email', old('email')) }}"
                        style="box-shadow:none;"
                        required>

                </div>

            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">Password</label>

                <div class="input-group">

                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-lock-fill" style="color:#387478;"></i>
                    </span>

                    <input type="password"
                        id="password"
                        name="password"
                        class="form-control border-start-0 border-end-0 py-2"
                        placeholder="Password Anda"
                        style="box-shadow:none;"
                        required>

                    <span class="input-group-text bg-white border-start-0"
                        style="cursor:pointer;"
                        onclick="togglePassword()">

                        <i class="bi bi-eye-slash-fill"
                            id="iconPassword"
                            style="color:#387478;"></i>

                    </span>

                </div>

            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div class="form-check">

                    <input class="form-check-input"
                        type="checkbox"
                        id="remember"
                        name="remember"
                        style="border-color:#387478;">

                    <label class="form-check-label"
                        for="remember">

                        Ingat Saya

                    </label>

                </div>

                <a href="#"
                    class="fw-semibold text-decoration-none"
                    style="color:#243642;">

                    Lupa Password?

                </a>

            </div>

            <button
                type="submit"
                class="btn w-100 text-white fw-semibold py-2 rounded-3"
                style="background:#243642;border:none;transition:.3s;"
                onmouseover="this.style.background='#387478'"
                onmouseout="this.style.background='#243642'">

                <i class="bi bi-box-arrow-in-right me-2"></i>Login

            </button>

        </form>

        <div class="text-center mt-4">

            <span class="text-muted">
                Belum punya akun?
            </span>

            <a href="{{ route('register') }}"
                class="fw-bold text-decoration-none"
                style="color:#243642;">

                Daftar

            </a>

        </div>

    </div>

</div>

<script>
    function togglePassword() {
        let password = document.getElementById('password');
        let icon = document.getElementById('iconPassword');
        if (password.type === "password") {
            password.type = "text";
            icon.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
        } else {
            password.type = "password";
            icon.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
        }
    }
</script>

@endsection