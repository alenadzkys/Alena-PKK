<?php
session_start();

require_once "../config/database.php";

if (isset($_SESSION['id_user'])) {
    header("Location: home_cofna.php");
    exit;
}

$pesan = $_GET['pesan'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - COFNA</title>
    <link rel="stylesheet" href="../css_cofna/auth.css">
</head>

<body>

<div class="auth-container">
    <div class="auth-form">

        <div class="auth-logo">COFNA</div>

        <h2>Daftar Akun</h2>
        <p class="auth-subtitle">
            Buat akun COFNA kamu!
        </p>

        <?php if ($pesan === 'email_ada'): ?>
            <p class="login-error">
                Email sudah terdaftar. Gunakan email lain.
            </p>
        <?php elseif ($pesan === 'gagal'): ?>
            <p class="login-error">
                Pendaftaran gagal. Coba lagi, ya.
            </p>
        <?php endif; ?>

        <form action="proses_daftar.php" method="POST">

            <div class="auth-group">
                <label for="nama">Nama</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Masukkan nama kamu"
                    maxlength="100"
                    required
                >
            </div>

            <div class="auth-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email"
                    maxlength="150"
                    required
                >
            </div>

            <div class="auth-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 8 karakter"
                    minlength="8"
                    required
                >
            </div>

            <div class="auth-group">
        <label for="konfirmasi_password">
                Konfirmasi Password
    </label>

    <input
        type="password"
        id="konfirmasi_password"
        name="konfirmasi_password"
        placeholder="Masukkan ulang password"
        minlength="8"
        required
    >

    <small id="pesan-password"></small>
</div>

            <button type="submit" class="auth-submit">
                Daftar Akun
            </button>

        </form>

        <p class="auth-switch">
            Sudah punya akun?
            <a href="login.php">Login di sini</a>
        </p>

    </div>
</div>

<script>
const form = document.querySelector('form');
const password = document.getElementById('password');
const konfirmasi = document.getElementById('konfirmasi_password');
const pesan = document.getElementById('pesan-password');

function cekPassword() {
    if (konfirmasi.value === '') {
        pesan.textContent = '';
        konfirmasi.setCustomValidity('');
        return;
    }

    if (password.value !== konfirmasi.value) {
        pesan.textContent = 'Password tidak sama!';
        pesan.style.color = '#c0392b';
        konfirmasi.setCustomValidity('Password dan konfirmasi password tidak sama.');
    } else {
        pesan.textContent = 'Password cocok!';
        pesan.style.color = '#287a3d';
        konfirmasi.setCustomValidity('');
    }
}

password.addEventListener('input', cekPassword);
konfirmasi.addEventListener('input', cekPassword);

form.addEventListener('submit', function(event) {
    cekPassword();

    if (!form.checkValidity()) {
        event.preventDefault();
        form.reportValidity();
    }
});
</script>

</body>
</html>