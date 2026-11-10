<?php
session_start();

if (
    !isset($_SESSION['id_user']) ||
    !isset($_SESSION['hak_akses']) ||
    $_SESSION['hak_akses'] !== 'admin'
) {
    header("Location: ../html_cofna/login.php");
    exit;
}

require_once "../config/database.php";
/** @var mysqli $conn */

$nama_admin = $_SESSION['nama'];

// Pilihan status pesanan
$status_pesanan = [
    'proses'  => 'Siap Diproses',
    'buat'    => 'Sedang Dibuat',
    'diambil' => 'Siap Diambil',
    'selesai' => 'Selesai'
];

// Proses update status pesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_pesanan = filter_input(
        INPUT_POST,
        'id_pesanan',
        FILTER_VALIDATE_INT
    );

    $aksi = $_POST['aksi'] ?? '';

    if ($id_pesanan && isset($status_pesanan[$aksi])) {

        $status_baru = $status_pesanan[$aksi];

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE tb_pesanan
             SET status = ?
             WHERE id_pesanan = ?"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $status_baru,
                $id_pesanan
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: kelola_pesanan.php");
    exit;
}

// Ambil semua pesanan
$query = "
    SELECT *
    FROM tb_pesanan
    ORDER BY id_pesanan DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Pesanan - COFNA Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
          rel="stylesheet">

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

        /* SIDEBAR */
        .sidebar {
            width: 285px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #4b2e20, #6b3e23);
            color: white;
            padding: 35px 22px;
            overflow-y: auto;
        }

        .brand {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-logo {
            width: 65px;
            height: 65px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand h1 {
            font-family: 'Playfair Display', serif;
            font-size: 27px;
            letter-spacing: 2px;
        }

        .brand p {
            font-size: 13px;
            opacity: 0.7;
            margin-top: 3px;
        }

        .menu-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            opacity: 0.5;
            margin: 0 12px 12px;
        }

        .side-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .side-menu a {
            color: white;
            text-decoration: none;
            padding: 13px 15px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 13px;
            font-size: 14px;
            transition: 0.25s;
        }

        .side-menu a:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        .side-menu a.active {
            background: rgba(255, 255, 255, 0.18);
        }

        .side-icon {
            width: 25px;
            text-align: center;
            font-size: 18px;
        }

        .divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.15);
            margin: 28px 10px;
        }

        /* CONTENT */
        .main {
            margin-left: 285px;
            padding: 45px;
            min-width: 0;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .top h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #3d2418;
        }

        .top p {
            color: #8b7568;
            font-size: 14px;
            margin-top: 5px;
        }

        .admin-name {
            background: white;
            padding: 12px 18px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(60, 35, 20, 0.06);
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* TABLE */
        .table-box {
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(60, 35, 20, 0.07);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1150px;
        }

        th {
            text-align: left;
            padding: 15px 12px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #8b7568;
            border-bottom: 1px solid #eee4dc;
            white-space: nowrap;
        }

        td {
            padding: 17px 12px;
            border-bottom: 1px solid #f1e9e3;
            font-size: 14px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .order-number {
            font-weight: 700;
            color: #6b3e23;
        }

        .customer-name {
            font-weight: 600;
        }

        .phone {
            color: #806d61;
            font-size: 13px;
        }

        .payment {
            font-size: 13px;
            font-weight: 600;
        }

        .total {
            font-weight: 700;
            color: #6b3e23;
            white-space: nowrap;
        }

        .date {
            font-size: 12px;
            color: #8b7568;
            white-space: nowrap;
        }

        /* STATUS BADGE */
        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #f3e7dc;
            color: #6b3e23;
            white-space: nowrap;
        }

        /* KOLOM AKSI */
        .action-cell {
            min-width: 340px;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
        }

        .action-buttons form {
            margin: 0;
        }

        /* TOMBOL UBAH STATUS */
        .btn-status {
            border: none;
            padding: 8px 10px;
            border-radius: 8px;
            color: white;
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            white-space: nowrap;
        }

        .btn-status:hover {
            filter: brightness(0.9);
            transform: translateY(-1px);
        }

        .btn-status.aktif {
            outline: 2px solid #d9c3af;
            outline-offset: 2px;
        }

        .btn-proses {
            background: #6b3e23;
        }

        .btn-buat {
            background: #b97732;
        }

        .btn-diambil {
            background: #477b61;
        }

        .btn-selesai {
            background: #526c91;
        }

        /* TOMBOL DETAIL */
        .btn-detail {
            display: inline-block;
            padding: 8px 13px;
            border-radius: 9px;
            background: #6b3e23;
            color: white;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
            white-space: nowrap;
        }

        .btn-detail:hover {
            background: #4b2e20;
            transform: translateY(-1px);
        }

        /* EMPTY STATE */
        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #8b7568;
        }

        .empty-icon {
            font-size: 45px;
            margin-bottom: 12px;
        }

        .empty h3 {
            color: #3d2418;
            margin-bottom: 8px;
        }

        .empty p {
            font-size: 14px;
        }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .sidebar {
                width: 220px;
                padding: 30px 15px;
            }

            .main {
                margin-left: 220px;
                padding: 25px;
            }

            .top h2 {
                font-size: 28px;
            }
        }

        @media (max-width: 650px) {
            .sidebar {
                width: 75px;
                padding: 25px 10px;
            }

            .brand h1 {
                font-size: 16px;
                letter-spacing: 1px;
            }

            .brand p,
            .menu-title,
            .side-menu a span:last-child {
                display: none;
            }

            .brand-logo {
                width: 45px;
                height: 45px;
            }

            .side-menu a {
                justify-content: center;
                padding: 12px 5px;
            }

            .side-icon {
                width: auto;
            }

            .main {
                margin-left: 75px;
                padding: 18px;
            }

            .top {
                align-items: flex-start;
                flex-direction: column;
            }

            .top h2 {
                font-size: 25px;
            }

            .table-box {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="brand">
        <div class="brand-logo">
            <img src="../image_cofna/cofna_logo.png"
                 alt="Logo COFNA">
        </div>

        <h1>COFNA</h1>
        <p>Admin Panel</p>
    </div>

    <div class="menu-title">Menu</div>

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

        <a href="../html_cofna/home_cofna.php">
            <span class="side-icon">🏠</span>
            <span>Ke Home</span>
        </a>

        <a href="../html_cofna/logout.php">
            <span class="side-icon">🚪</span>
            <span>Logout</span>
        </a>

    </div>

</aside>


<!-- MAIN CONTENT -->
<main class="main">

    <div class="top">

        <div>
            <h2>Kelola Pesanan</h2>
            <p>Lihat dan kelola pesanan pelanggan COFNA.</p>
        </div>

        <div class="admin-name">
            👤 <?= htmlspecialchars($nama_admin); ?>
        </div>

    </div>


    <!-- TABEL PESANAN -->
    <div class="table-box">

        <?php if ($result && mysqli_num_rows($result) > 0): ?>

            <table>

                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pemesan</th>
                        <th>Telepon</th>
                        <th>Pembayaran</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($pesanan = mysqli_fetch_assoc($result)): ?>

                        <?php
                        $status_sekarang = $pesanan['status'];
                        ?>

                        <tr>

                            <!-- NOMOR PESANAN -->
                            <td>
                                <div class="order-number">
                                    <?= htmlspecialchars(
                                        $pesanan['nomor_pesanan'] ?? ''
                                    ); ?>
                                </div>
                            </td>

                            <!-- NAMA PEMESAN -->
                            <td>
                                <div class="customer-name">
                                    <?= htmlspecialchars(
                                        $pesanan['nama_pemesan'] ?? ''
                                    ); ?>
                                </div>
                            </td>

                            <!-- TELEPON -->
                            <td>
                                <div class="phone">
                                    <?= htmlspecialchars(
                                        $pesanan['telepon'] ?? ''
                                    ); ?>
                                </div>
                            </td>

                            <!-- PEMBAYARAN -->
                            <td>
                                <div class="payment">
                                    <?= htmlspecialchars(
                                        $pesanan['metode_pembayaran'] ?? ''
                                    ); ?>
                                </div>
                            </td>

                            <!-- TOTAL -->
                            <td>
                                <div class="total">
                                    Rp <?= number_format(
                                        (float) ($pesanan['total'] ?? 0),
                                        0,
                                        ',',
                                        '.'
                                    ); ?>
                                </div>
                            </td>

                            <!-- STATUS -->
                            <td>
                                <span class="status">
                                    <?= htmlspecialchars($status_sekarang); ?>
                                </span>
                            </td>

                            <!-- TANGGAL -->
                            <td>
                                <div class="date">
                                    <?= !empty($pesanan['tanggal_pesanan'])
                                        ? date(
                                            'd/m/Y H:i',
                                            strtotime($pesanan['tanggal_pesanan'])
                                        )
                                        : '-'; ?>
                                </div>
                            </td>

                            <!-- TOMBOL AKSI -->
                            <td class="action-cell">

                                <div class="action-buttons">

                                    <!-- SIAP DIPROSES -->
                                    <form method="POST"
                                          action="kelola_pesanan.php">

                                        <input type="hidden"
                                               name="id_pesanan"
                                               value="<?= (int) $pesanan['id_pesanan']; ?>">

                                        <button type="submit"
                                                name="aksi"
                                                value="proses"
                                                class="btn-status btn-proses <?= $status_sekarang === 'Siap Diproses' ? 'aktif' : ''; ?>"
                                                title="Siap Diproses">
                                            Proses
                                        </button>

                                    </form>


                                    <!-- SEDANG DIBUAT -->
                                    <form method="POST"
                                          action="kelola_pesanan.php">

                                        <input type="hidden"
                                               name="id_pesanan"
                                               value="<?= (int) $pesanan['id_pesanan']; ?>">

                                        <button type="submit"
                                                name="aksi"
                                                value="buat"
                                                class="btn-status btn-buat <?= $status_sekarang === 'Sedang Dibuat' ? 'aktif' : ''; ?>"
                                                title="Sedang Dibuat">
                                            Buat
                                        </button>

                                    </form>


                                    <!-- SIAP DIAMBIL -->
                                    <form method="POST"
                                          action="kelola_pesanan.php">

                                        <input type="hidden"
                                               name="id_pesanan"
                                               value="<?= (int) $pesanan['id_pesanan']; ?>">

                                        <button type="submit"
                                                name="aksi"
                                                value="diambil"
                                                class="btn-status btn-diambil <?= $status_sekarang === 'Siap Diambil' ? 'aktif' : ''; ?>"
                                                title="Siap Diambil">
                                            Diambil
                                        </button>

                                    </form>


                                    <!-- SELESAI -->
                                    <form method="POST"
                                          action="kelola_pesanan.php">

                                        <input type="hidden"
                                               name="id_pesanan"
                                               value="<?= (int) $pesanan['id_pesanan']; ?>">

                                        <button type="submit"
                                                name="aksi"
                                                value="selesai"
                                                class="btn-status btn-selesai <?= $status_sekarang === 'Selesai' ? 'aktif' : ''; ?>"
                                                title="Selesai">
                                            Selesai
                                        </button>

                                    </form>


                                    <!-- DETAIL TETAP ADA -->
                                    <a href="detail_pesanan.php?id=<?= (int) $pesanan['id_pesanan']; ?>"
                                       class="btn-detail">
                                        Detail
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty">
                <div class="empty-icon">🧾</div>
                <h3>Belum Ada Pesanan</h3>
                <p>Pesanan pelanggan akan muncul di sini.</p>
            </div>

        <?php endif; ?>

    </div>

</main>

</body>
</html>