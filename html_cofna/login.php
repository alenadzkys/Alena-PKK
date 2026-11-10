<?php
session_start();

require_once "../config/database.php";

if (isset($_SESSION['id_user'])) {
    header("Location: home_cofna.php");
    exit;
}

$error = $_GET['error'] ?? '';
$daftar = $_GET['daftar'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | COFNA</title>
    <link rel="stylesheet" href="../css_cofna/auth.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-form">

        <div class="auth-logo">COFNA</div>

        <h2>Login</h2>
        <p class="auth-subtitle">
            Selamat datang kembali! Silakan masuk ke akun kamu.
        </p>

        <?php if ($error === '1'): ?>
            <p class="login-error">
                Email atau password salah.
            </p>
        <?php endif; ?>

        <?php if ($daftar === 'berhasil'): ?>
            <p class="login-success">
                Pendaftaran berhasil! Silakan login.
            </p>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">

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
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <button type="submit" class="auth-submit">
                Login
            </button>

        </form>

        <p class="auth-switch">
            Belum punya akun?
            <a href="daftar.php">Daftar sekarang</a>
        </p>
        
    </div>
</div>

</body>
</html>