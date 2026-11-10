<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once "../config/database.php";

if (!isset($conn) && isset($koneksi)) {
    $conn = $koneksi;
}

if (!isset($conn)) {
    exit("Koneksi database tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: daftar.php");
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$konfirmasi = $_POST['konfirmasi_password'] ?? '';

if ($nama === '') {
    exit("Nama wajib diisi.");
}
if (strlen($nama) > 100) {
    exit("Nama maksimal 100 karakter.");
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Format email tidak valid.");
}
if (strlen($email) > 150) {
    exit("Email maksimal 150 karakter.");
}
if (strlen($password) < 8) {
    exit("Password minimal 8 karakter.");
}
if ($password !== $konfirmasi) {
    exit("Konfirmasi password tidak sama.");
}
try {

    // Periksa apakah email sudah terdaftar
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id_user FROM tb_user WHERE email = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        header("Location: daftar.php?pesan=email_ada");
        exit;
    }

    mysqli_stmt_close($stmt);

    // Simpan password dengan aman
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $hakAkses = 'user';

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO tb_user (nama, email, password, hak_akses)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ssss",
        $nama,
        $email,
        $passwordHash,
        $hakAkses
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Pastikan akun benar-benar tersimpan
    $cek = mysqli_prepare(
        $conn,
        "SELECT id_user FROM tb_user WHERE email = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($cek, "s", $email);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) === 1) {
        mysqli_stmt_close($cek);
        header("Location: login.php?daftar=berhasil");
        exit;
    }

    mysqli_stmt_close($cek);
    exit("Akun belum ditemukan setelah proses penyimpanan.");

} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Database error: " . htmlspecialchars($e->getMessage()));
}
?>