<?php

session_start();
require_once "../config/database.php";

// Menyesuaikan nama koneksi
if (!isset($conn) && isset($koneksi)) {
    $conn = $koneksi;
}

if (!isset($conn)) {
    die("Koneksi database tidak ditemukan.");
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// Cari akun berdasarkan email
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM tb_user WHERE email = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$loginBerhasil = false;

if ($user) {

    // Mendukung password hash dan akun lama
    if (password_verify($password, $user['password'])) {
        $loginBerhasil = true;

    } elseif ($password === $user['password']) {
        // Password akun lama masih berupa teks biasa
        $loginBerhasil = true;

        // Ubah password akun tersebut menjadi hash
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $update = mysqli_prepare(
            $conn,
            "UPDATE tb_user SET password = ? WHERE id_user = ?"
        );

        mysqli_stmt_bind_param(
            $update,
            "si",
            $hash,
            $user['id_user']
        );

        mysqli_stmt_execute($update);
        mysqli_stmt_close($update);
    }
}

if ($loginBerhasil) {

    session_regenerate_id(true);

    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['hak_akses'] = $user['hak_akses'];

    if ($user['hak_akses'] === 'admin') {
        header("Location: ../admin/index.php");
        exit;
    }

    header("Location: home_cofna.php");
    exit;

}

header("Location: login.php?error=1");
exit;

?>
```