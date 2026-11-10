<?php

session_start();

if (
    !isset($_SESSION['id_user']) ||
    $_SESSION['hak_akses'] !== 'admin'
) {
    header("Location: ../html_cofna/login.php");
    exit;
}

require_once "../config/database.php";
/** @var mysqli $conn */


$id_pesanan = (int)($_POST['id_pesanan'] ?? 0);
$status = trim($_POST['status'] ?? '');


$status_valid = [
    "Siap Diproses",
    "Sedang Dibuat",
    "Siap Diambil",
    "Selesai"
];


if (
    $id_pesanan <= 0 ||
    !in_array($status, $status_valid, true)
) {
    die("Data status tidak valid.");
}


// UPDATE STATUS

$query = "
    UPDATE tb_pesanan
    SET status = ?
    WHERE id_pesanan = ?
";


$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $status,
    $id_pesanan
);


if (mysqli_stmt_execute($stmt)) {

    header(
        "Location: detail_pesanan.php?id=" .
        $id_pesanan
    );

    exit;

} else {

    die(
        "Gagal mengubah status pesanan: " .
        mysqli_error($conn)
    );
}

?>