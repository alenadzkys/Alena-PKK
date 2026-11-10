<?php

session_start();

require_once "../config/database.php";

/** @var mysqli $conn */

// =========================
// CEK LOGIN
// =========================

if (!isset($_SESSION['id_user'])) {

    header("Location: ../html_cofna/login.php");

    exit;
}


// =========================
// CEK ADMIN
// =========================

if (($_SESSION['hak_akses'] ?? '') !== 'admin') {

    header("Location: ../html_cofna/home_cofna.php");

    exit;
}


// =========================
// CEK ID MENU
// =========================

$id_menu = $_GET['id'] ?? 0;

$id_menu = intval($id_menu);


if ($id_menu <= 0) {

    header("Location: kelola_menu.php");

    exit;
}


// =========================
// AMBIL DATA MENU
// =========================

$stmt = mysqli_prepare(
    $conn,
    "SELECT gambar
     FROM tb_menu
     WHERE id_menu = ?"
);


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_menu
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$menu = mysqli_fetch_assoc($result);


// =========================
// CEK MENU
// =========================

if (!$menu) {

    header("Location: kelola_menu.php");

    exit;
}


// =========================
// HAPUS DATA DATABASE
// =========================

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM tb_menu
     WHERE id_menu = ?"
);


mysqli_stmt_bind_param(
    $delete,
    "i",
    $id_menu
);


if (mysqli_stmt_execute($delete)) {


    // =========================
    // HAPUS GAMBAR
    // =========================

    if (!empty($menu['gambar'])) {

        $file_gambar =
            "../image_cofna/" .
            $menu['gambar'];


        if (file_exists($file_gambar)) {

            unlink($file_gambar);

        }

    }


    // =========================
    // KEMBALI KE KELOLA MENU
    // =========================

    header("Location: kelola_menu.php");

    exit;

} else {

    die(
        "Gagal menghapus menu: " .
        mysqli_error($conn)
    );

}

?>