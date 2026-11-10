<?php

session_start();

header("Content-Type: application/json");

require_once "../config/database.php";
/** @var mysqli $conn */

if (!isset($_SESSION['id_user'])) {
    echo json_encode([
        "success" => false,
        "message" => "Kamu harus login terlebih dahulu."
    ]);
    exit;
}

$nomor_pesanan = trim($_GET['nomor_pesanan'] ?? "");

if ($nomor_pesanan === "") {
    echo json_encode([
        "success" => false,
        "message" => "Nomor pesanan tidak ditemukan."
    ]);
    exit;
}

$id_user = $_SESSION['id_user'];

$query = "
    SELECT
        nomor_pesanan,
        metode_pembayaran,
        total,
        status,
        tanggal_pesanan
    FROM tb_pesanan
    WHERE nomor_pesanan = ?
    AND id_user = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $nomor_pesanan,
    $id_user
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Pesanan tidak ditemukan."
    ]);

    exit;
}

$pesanan = mysqli_fetch_assoc($result);

echo json_encode([
    "success" => true,
    "order" => $pesanan
]);

?>