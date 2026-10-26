@extends('layouts.app')

@section('title','Register')

@section('content')

<div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center py-5" style="background:linear-gradient(rgba(9,20,19,.60),rgba(9,20,19,.60)),url('{{ asset('images/login.jpeg') }}') center/cover no-repeat;">

    <div class="card border-0 rounded-4 shadow-lg p-4" style="width:440px;max-width:95%;background:rgba(255,255,255,.95);backdrop-filter:blur(12px);box-shadow:0 18px 40px rgba(9,20,19,.25);">

        <div class="text-center mb-4">
            <img src="{{ asset('images/logo1.jpg') }}" class="rounded-circle shadow mb-3" style="width:90px;height:90px;object-fit:cover;">
            <h2 class="fw-bold mb-1" style="color:#243642;">DAFTAR AKUN</h2>
            <p class="text-muted mb-0">Silakan lengkapi data di bawah ini</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success rounded-3 border-0 shadow-sm p-3 mb-3" style="background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0!important;font-size:0.88rem;">
            <i class="bi bi-check-circle-fill me-1.5"></i>{{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm p-3 mb-3" style="background:#FEF2F2;color:#991B1B;border:1px solid #FCA5A5!important;font-size:0.88rem;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register.proses') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold small">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-fill" style="color:#387478;"></i></span>
                    <input type="text" name="nama" class="form-control border-start-0 py-2" placeholder="Masukkan Nama Lengkap" value="{{ old('nama') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope-fill" style="color:#387478;"></i></span>
                    <input type="email" name="email" class="form-control border-start-0 py-2" placeholder="Masukkan Email" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Nomor HP</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-telephone-fill" style="color:#387478;"></i></span>
                    <input type="text" name="no_hp" class="form-control border-start-0 py-2" placeholder="Masukkan Nomor HP" value="{{ old('no_hp') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock-fill" style="color:#387478;"></i></span>
                    <input type="password" id="password" name="password" class="form-control border-start-0 border-end-0 py-2" placeholder="Masukkan Password" required>
                    <span class="input-group-text bg-white border-start-0" style="cursor:pointer" onclick="togglePassword('password','icon1')">
                        <i class="bi bi-eye-slash-fill" id="icon1" style="color:#387478;"></i>
                    </span>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Konfirmasi Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-shield-lock-fill" style="color:#387478;"></i></span>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control border-start-0 border-end-0 py-2" placeholder="Konfirmasi Password" required>
                    <span class="input-group-text bg-white border-start-0" style="cursor:pointer" onclick="togglePassword('password_confirmation','icon2')">
                        <i class="bi bi-eye-slash-fill" id="icon2" style="color:#387478;"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn w-100 text-white fw-semibold py-2 rounded-3" style="background:#243642;border:none;transition:.3s;" onmouseover="this.style.background='#387478'" onmouseout="this.style.background='#243642'">
                <i class="bi bi-person-plus-fill me-2"></i>Daftar Akun
            </button>

        </form>

        <div class="text-center mt-4">
            <span class="text-muted">Sudah memiliki akun?</span>
            <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color:#243642;">Masuk</a>
        </div>

    </div>

</div>

<script>
    function togglePassword(id, iconId) {
        let input = document.getElementById(id);
        let icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
        } else {
            input.type = "password";
            icon.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
        }
    }
</script>

@endsection