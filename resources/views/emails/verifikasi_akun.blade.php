<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Berhasil</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #243642; margin: 0;">Glamping Danau Kembar Scenery</h2>
            <p style="color: #6c757d; font-size: 14px; margin-top: 5px;">Alahan Panjang, Solok, Sumatera Barat</p>
        </div>
        <hr style="border: 0; border-top: 1px solid #eeeeee; margin: 20px 0;">
        
        <h3 style="color: #28a745;">Halo, {{ $user->nama }}! 👋</h3>
        <p style="color: #495057; line-height: 1.6;">
            Selamat! Pendaftaran akun Anda di <strong>Glamping Danau Kembar Scenery</strong> telah <strong>berhasil & akun Anda sudah aktif</strong>.
        </p>
        <p style="color: #495057; line-height: 1.6;">
            Silakan klik tombol di bawah ini untuk masuk dan langsung melakukan pemesanan unit glamping kami.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('login', ['email' => $user->email]) }}" style="background-color: #387478; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 25px; font-weight: bold; display: inline-block;">
                Masuk / Login Sekarang
            </a>
        </div>

        <p style="color: #6c757d; font-size: 13px; line-height: 1.5;">
            Detail Akun Anda:<br>
            • <strong>Nama:</strong> {{ $user->nama }}<br>
            • <strong>Email:</strong> {{ $user->email }}<br>
            • <strong>Status:</strong> Aktif
        </p>

        <hr style="border: 0; border-top: 1px solid #eeeeee; margin: 20px 0;">
        <p style="color: #adb5bd; font-size: 12px; text-align: center;">
            &copy; {{ date('Y') }} Glamping Danau Kembar Scenery. Email ini dikirim secara otomatis.
        </p>
    </div>
</body>
</html>
