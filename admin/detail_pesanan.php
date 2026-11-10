<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['hak_akses'] !== 'admin') {
    header("Location: ../html_cofna/login.php");
    exit;
}

require_once "../config/database.php";
/** @var mysqli $conn */

// Ambil ID pesanan dari URL
$id_pesanan = (int)($_GET['id'] ?? 0);

if ($id_pesanan <= 0) {
    die("ID pesanan tidak valid.");
}

// =========================
// AMBIL DATA PESANAN
// =========================

$queryPesanan = "
    SELECT *
    FROM tb_pesanan
    WHERE id_pesanan = ?
";

$stmtPesanan = mysqli_prepare($conn, $queryPesanan);
mysqli_stmt_bind_param($stmtPesanan, "i", $id_pesanan);
mysqli_stmt_execute($stmtPesanan);

$resultPesanan = mysqli_stmt_get_result($stmtPesanan);
$pesanan = mysqli_fetch_assoc($resultPesanan);

if (!$pesanan) {
    die("Pesanan tidak ditemukan.");
}

// =========================
// AMBIL DETAIL PESANAN
// =========================

$queryDetail = "
    SELECT *
    FROM tb_detail_pesanan
    WHERE id_pesanan = ?
    ORDER BY id_detail ASC
";

$stmtDetail = mysqli_prepare($conn, $queryDetail);
mysqli_stmt_bind_param($stmtDetail, "i", $id_pesanan);
mysqli_stmt_execute($stmtDetail);

$resultDetail = mysqli_stmt_get_result($stmtDetail);

$nama_admin = htmlspecialchars($_SESSION['nama'] ?? 'Admin');
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail Pesanan - COFNA Admin</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8f3ed;
            color: #3d2418;
        }

        a {
            text-decoration: none;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            width: 285px;
            min-height: 100vh;

            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;

            background: linear-gradient(
                180deg,
                #4b291b,
                #633b27,
                #4b291b
            );

            color: white;
            padding: 30px 22px;
        }

        .brand {
            text-align: center;
            margin-bottom: 55px;
        }

        .brand-logo {
            width: 76px;
            height: 76px;

            background: #f8e8d4;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            overflow: hidden;
            margin: 0 auto;

            box-shadow:
                0 10px 25px rgba(0,0,0,.15);
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            letter-spacing: 2px;
            margin-top: 12px;
        }

        .brand p {
            color: #e6c9b1;
            margin-top: 4px;
            font-size: 14px;
        }

        .menu-title {
            color: #d5b9a5;
            font-size: 13px;
            margin: 0 12px 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .side-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .side-menu a {
            color: #fff;
            padding: 15px 16px;
            border-radius: 16px;

            display: flex;
            align-items: center;
            gap: 14px;

            font-size: 15px;
            transition: .25s;
        }

        .side-menu a:hover {
            background: rgba(255,255,255,.10);
            transform: translateX(4px);
        }

        .side-menu a.active {
            background: #a96f4c;

            box-shadow:
                0 8px 20px rgba(0,0,0,.12);
        }

        .side-icon {
            width: 25px;
            text-align: center;
            font-size: 20px;
        }

        .divider {
            height: 1px;
            background: rgba(255,255,255,.18);
            margin: 30px 8px;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 285px;
            width: calc(100% - 285px);
            min-height: 100vh;
            padding: 32px 38px 50px;
        }

        /* =========================
           TOPBAR
        ========================= */

        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 28px;
        }

        .admin-profile {
            background: white;
            padding: 10px 18px 10px 10px;
            border-radius: 30px;

            display: flex;
            align-items: center;
            gap: 11px;

            box-shadow:
                0 8px 25px rgba(95,60,40,.08);
        }

        .admin-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;

            background: #f1dfcb;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 21px;
        }

        .admin-profile strong {
            font-size: 14px;
        }

        .admin-profile span {
            display: block;
            color: #987b68;
            font-size: 12px;
            margin-top: 2px;
        }

        /* =========================
           HEADER
        ========================= */

        .page-header {
            margin-bottom: 25px;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 18px;

            color: #7b5038;
            font-size: 14px;
            font-weight: 600;
        }

        .back-btn:hover {
            color: #4b291b;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            color: #4b291b;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #806c5e;
            font-size: 15px;
        }

        /* =========================
           INFO CARD
        ========================= */

        .info-card {
            background: white;
            border-radius: 24px;
            padding: 28px;

            box-shadow:
                0 12px 30px rgba(91,55,35,.07);

            margin-bottom: 25px;
        }

        .info-title {
            font-family: 'Playfair Display', serif;
            font-size: 23px;
            margin-bottom: 22px;
            color: #4b291b;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            background: #fffaf5;
            border-radius: 15px;
            padding: 16px;
        }

        .info-item label {
            display: block;
            color: #987b68;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .info-item strong {
            font-size: 15px;
            color: #4b291b;
        }

        .order-number {
            color: #a96f4c !important;
            font-size: 18px !important;
        }

        /* =========================
           STATUS
        ========================= */

        .status {
            display: inline-block;

            padding: 7px 13px;

            background: #f1dfcb;
            color: #6b3e23;

            border-radius: 20px;

            font-size: 12px;
            font-weight: 700;
        }

        /* =========================
           DETAIL TABLE
        ========================= */

        .detail-card {
            background: white;
            border-radius: 24px;
            padding: 28px;

            box-shadow:
                0 12px 30px rgba(91,55,35,.07);
        }

        .detail-title {
            font-family: 'Playfair Display', serif;
            font-size: 23px;
            margin-bottom: 20px;
            color: #4b291b;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th {
            text-align: left;
            padding: 14px 12px;

            color: #987b68;
            font-size: 12px;

            text-transform: uppercase;
            letter-spacing: .5px;

            border-bottom: 1px solid #eadfd5;
        }

        td {
            padding: 16px 12px;

            font-size: 14px;

            border-bottom: 1px solid #f1e8e1;
        }

        .menu-name {
            font-weight: 700;
            color: #4b291b;
        }

        .price,
        .subtotal {
            color: #6b3e23;
            font-weight: 600;
        }

        .qty {
            text-align: center;
            font-weight: 600;
        }

        .total-row td {
            border-bottom: none;
            padding-top: 25px;
        }

        .total-label {
            text-align: right;
            font-weight: 600;
            color: #806c5e;
        }

        .grand-total {
            color: #6b3e23;
            font-size: 20px;
            font-weight: 700;
        }

        /* =========================
           NOTE
        ========================= */

        .note-box {
            margin-top: 22px;
            padding: 18px;

            background: #fff8f1;
            border-radius: 15px;
        }

        .note-box strong {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .note-box p {
            color: #806c5e;
            font-size: 14px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {

            .sidebar {
                width: 230px;
            }

            .main {
                margin-left: 230px;
                width: calc(100% - 230px);
                padding: 25px;
            }

        }

        @media (max-width: 650px) {

            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
            }

            .main {
                margin-left: 0;
                width: 100%;
                padding: 22px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

        }

        .status-select {
    padding: 10px 14px;
    border: 1px solid #d9c6b6;
    border-radius: 12px;
    background: #fffaf5;
    color: #4b291b;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}

.status-select:focus {
    outline: none;
    border-color: #a96f4c;
    box-shadow: 0 0 0 4px rgba(169,111,76,.10);
}

.btn-status {
    margin-left: 8px;
    padding: 10px 15px;
    border: none;
    border-radius: 12px;
    background: #673e28;
    color: white;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s;
}

.btn-status:hover {
    background: #7c4d31;
    transform: translateY(-1px);
}

    </style>

</head>


<body>

<div class="dashboard">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="brand">

            <div class="brand-logo">
                <img
                    src="../image_cofna/cofna_logo.png"
                    alt="Logo COFNA"
                >
            </div>

            <h1>COFNA</h1>

            <p>Admin Panel</p>

        </div>


        <div class="menu-title">
            Menu
        </div>


        <div class="side-menu">

            <a href="index.php">
                <span class="side-icon">🏠</span>
                <span>Beranda</span>
            </a>

            <a href="kelola_menu.php">
                <span class="side-icon">☕</span>
                <span>Kelola Menu</span>
            </a>

            <a href="tambah_menu.php">
                <span class="side-icon">＋</span>
                <span>Tambah Menu</span>
            </a>

            <a href="kelola_pesanan.php" class="active">
                <span class="side-icon">🧾</span>
                <span>Kelola Pesanan</span>
            </a>

        </div>


        <div class="divider"></div>


        <div class="side-menu">

            <a href="../menu.php">
                <span class="side-icon">👀</span>
                <span>Lihat Menu</span>
            </a>

            <a href="../html_cofna/home_cofna.php">
                <span class="side-icon">🏡</span>
                <span>Ke Home</span>
            </a>

            <a href="../html_cofna/logout.php">
                <span class="side-icon">🚪</span>
                <span>Logout</span>
            </a>

        </div>

    </aside>


    <!-- MAIN -->

    <main class="main">

        <div class="topbar">

            <div class="admin-profile">

                <div class="admin-avatar">
                    👤
                </div>

                <div>

                    <strong>
                        <?= $nama_admin ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </div>


        <!-- HEADER -->

        <div class="page-header">

            <a
                href="kelola_pesanan.php"
                class="back-btn"
            >
                ← Kembali ke Kelola Pesanan
            </a>

            <h1>
                Detail Pesanan 🧾
            </h1>

            <p>
                Informasi lengkap pesanan pelanggan COFNA.
            </p>

        </div>


        <!-- INFORMASI PESANAN -->

        <div class="info-card">

            <div class="info-title">
                Informasi Pesanan
            </div>


            <div class="info-grid">

                <div class="info-item">

                    <label>
                        Nomor Pesanan
                    </label>

                    <strong class="order-number">
                        <?= htmlspecialchars($pesanan['nomor_pesanan']); ?>
                    </strong>

                </div>


                <div class="info-item">

                    <label>
                        Status
                    </label>

                    <form action="ubah_status_pesanan.php" method="POST">

    <input
        type="hidden"
        name="id_pesanan"
        value="<?= $pesanan['id_pesanan']; ?>"
    >

    <select name="status" class="status-select">

        <option
            value="Siap Diproses"
            <?= $pesanan['status'] === 'Siap Diproses' ? 'selected' : ''; ?>
        >
            Siap Diproses
        </option>

        <option
            value="Sedang Dibuat"
            <?= $pesanan['status'] === 'Sedang Dibuat' ? 'selected' : ''; ?>
        >
            Sedang Dibuat
        </option>

        <option
            value="Siap Diambil"
            <?= $pesanan['status'] === 'Siap Diambil' ? 'selected' : ''; ?>
        >
            Siap Diambil
        </option>

        <option
            value="Selesai"
            <?= $pesanan['status'] === 'Selesai' ? 'selected' : ''; ?>
        >
            Selesai
        </option>

    </select>

    <button type="submit" class="btn-status">
        Simpan Status
    </button>

</form>

                </div>


                <div class="info-item">

                    <label>
                        Nama Pemesan
                    </label>

                    <strong>
                        <?= htmlspecialchars($pesanan['nama_pemesan']); ?>
                    </strong>

                </div>


                <div class="info-item">

                    <label>
                        Nomor Telepon
                    </label>

                    <strong>
                        <?= htmlspecialchars($pesanan['telepon']); ?>
                    </strong>

                </div>


                <div class="info-item">

                    <label>
                        Metode Pembayaran
                    </label>

                    <strong>
                        <?= htmlspecialchars($pesanan['metode_pembayaran']); ?>
                    </strong>

                </div>


                <div class="info-item">

                    <label>
                        Tanggal Pesanan
                    </label>

                    <strong>
                        <?= date(
                            'd/m/Y H:i',
                            strtotime($pesanan['tanggal_pesanan'])
                        ); ?>
                    </strong>

                </div>

            </div>


            <div class="note-box">

                <strong>
                    📝 Catatan Pemesan
                </strong>

                <p>

                    <?php
                    if (!empty($pesanan['catatan'])) {
                        echo htmlspecialchars($pesanan['catatan']);
                    } else {
                        echo "Tidak ada catatan.";
                    }
                    ?>

                </p>

            </div>

        </div>


        <!-- DETAIL MENU -->

        <div class="detail-card">

            <div class="detail-title">
                Detail Menu
            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>Menu</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>

                    </thead>


                    <tbody>

                        <?php while ($detail = mysqli_fetch_assoc($resultDetail)): ?>

                            <tr>

                                <td>
                                    <span class="menu-name">
                                        <?= htmlspecialchars($detail['nama_menu']); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="price">
                                        Rp <?= number_format(
                                            $detail['harga'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="qty">
                                        <?= (int)$detail['jumlah']; ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="subtotal">
                                        Rp <?= number_format(
                                            $detail['subtotal'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endwhile; ?>


                        <tr class="total-row">

                            <td colspan="3">

                                <div class="total-label">
                                    Total Pembayaran
                                </div>

                            </td>

                            <td>

                                <div class="grand-total">
                                    Rp <?= number_format(
                                        $pesanan['total'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>
                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>

</html>