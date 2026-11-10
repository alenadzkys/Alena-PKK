<?php

session_start();


// =========================
// CEK LOGIN
// =========================

if (!isset($_SESSION['id_user'])) {

    header("Location: login.php");
    exit;

}


// =========================
// KONEKSI DATABASE
// =========================

require_once "../config/database.php";

/** @var mysqli $conn */


$id_user = $_SESSION['id_user'];


// =========================
// AMBIL ID PESANAN
// =========================

$id_pesanan = (int)($_GET['id'] ?? 0);


if ($id_pesanan <= 0) {

    die("Pesanan tidak ditemukan.");

}


// =========================
// AMBIL DATA PESANAN
// =========================

$queryPesanan = "
    SELECT
        id_pesanan,
        nomor_pesanan,
        nama_pemesan,
        telepon,
        catatan,
        metode_pembayaran,
        total,
        status,
        tanggal_pesanan
    FROM tb_pesanan
    WHERE id_pesanan = ?
    AND id_user = ?
    LIMIT 1
";


$stmtPesanan = mysqli_prepare(
    $conn,
    $queryPesanan
);


mysqli_stmt_bind_param(
    $stmtPesanan,
    "ii",
    $id_pesanan,
    $id_user
);


mysqli_stmt_execute(
    $stmtPesanan
);


$resultPesanan =
    mysqli_stmt_get_result(
        $stmtPesanan
    );


$pesanan =
    mysqli_fetch_assoc(
        $resultPesanan
    );


if (!$pesanan) {

    die("Pesanan tidak ditemukan.");

}


// =========================
// AMBIL DETAIL MENU
// =========================

$queryDetail = "
    SELECT
        id_detail,
        nama_menu,
        harga,
        jumlah,
        subtotal
    FROM tb_detail_pesanan
    WHERE id_pesanan = ?
    ORDER BY id_detail ASC
";


$stmtDetail = mysqli_prepare(
    $conn,
    $queryDetail
);


mysqli_stmt_bind_param(
    $stmtDetail,
    "i",
    $id_pesanan
);


mysqli_stmt_execute(
    $stmtDetail
);


$resultDetail =
    mysqli_stmt_get_result(
        $stmtDetail
    );


// =========================
// STATUS CLASS
// =========================

$status = $pesanan['status'];

$status_class = "status-process";


if ($status === "Sedang Dibuat") {

    $status_class = "status-making";

}

elseif ($status === "Siap Diambil") {

    $status_class = "status-ready";

}

elseif ($status === "Selesai") {

    $status_class = "status-done";

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Detail Pesanan - COFNA
    </title>


    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet"
    >


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            background: #faf6f1;

            color: #4b291b;

            font-family:
                'DM Sans',
                sans-serif;

        }


        /* =========================
           NAVBAR
        ========================== */

        .navbar {

            height: 78px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 8%;

            background: #fffdfb;

            border-bottom:
                1px solid #eaded3;

        }


        .logo {

            font-family:
                'Playfair Display',
                serif;

            font-size: 32px;

            font-weight: 700;

            color: #633b27;

        }


        .logo span {

            color: #a96f4c;

        }


        .navbar nav {

            display: flex;

            align-items: center;

            gap: 28px;

        }


        .navbar nav a {

            color: #5a3a2a;

            text-decoration: none;

            font-size: 15px;

            font-weight: 600;

            transition: .2s;

        }


        .navbar nav a:hover {

            color: #a96f4c;

        }


        /* =========================
           PAGE
        ========================== */

        .detail-page {

            max-width: 900px;

            margin: auto;

            padding: 55px 25px 80px;

        }


        .back-link {

            display: inline-block;

            margin-bottom: 25px;

            color: #795238;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;

        }


        .back-link:hover {

            color: #a96f4c;

        }


        .detail-heading {

            margin-bottom: 30px;

        }


        .detail-heading span {

            color: #a96f4c;

            font-size: 12px;

            font-weight: 700;

            letter-spacing: 2px;

        }


        .detail-heading h1 {

            margin: 8px 0;

            font-family:
                'Playfair Display',
                serif;

            font-size: 40px;

            color: #4b291b;

        }


        .detail-heading p {

            margin: 0;

            color: #8b7567;

        }


        /* =========================
           MAIN CARD
        ========================== */

        .detail-card {

            background: #fffdfb;

            border:
                1px solid #eaded3;

            border-radius: 22px;

            padding: 28px;

            box-shadow:
                0 10px 30px
                rgba(75, 41, 27, .06);

        }


        /* =========================
           ORDER HEADER
        ========================== */

        .order-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            padding-bottom: 22px;

            border-bottom:
                1px solid #eee2d8;

        }


        .order-label {

            font-size: 12px;

            color: #9b8679;

            display: block;

            margin-bottom: 5px;

        }


        .order-number {

            font-size: 24px;

            font-weight: 700;

            color: #6b3e23;

        }


        /* =========================
           STATUS
        ========================== */

        .order-status {

            padding: 9px 16px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 700;

            white-space: nowrap;

        }


        .status-process {

            background: #f7eadc;

            color: #9a5d2f;

        }


        .status-making {

            background: #f4e5cf;

            color: #85501f;

        }


        .status-ready {

            background: #e8f0df;

            color: #55783e;

        }


        .status-done {

            background: #e3eee6;

            color: #487055;

        }


        /* =========================
           INFO
        ========================== */

        .order-info {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 20px;

            padding: 25px 0;

            border-bottom:
                1px solid #eee2d8;

        }


        .info-item span {

            display: block;

            color: #9b8679;

            font-size: 12px;

            margin-bottom: 6px;

        }


        .info-item strong {

            color: #4b291b;

            font-size: 15px;

        }


        /* =========================
           MENU TITLE
        ========================== */

        .menu-title {

            margin-top: 25px;

            margin-bottom: 15px;

            font-family:
                'Playfair Display',
                serif;

            font-size: 22px;

            color: #4b291b;

        }


        /* =========================
           MENU ITEM
        ========================== */

        .menu-item {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            padding: 17px 0;

            border-bottom:
                1px solid #eee2d8;

        }


        .menu-name {

            font-weight: 700;

            color: #5a3422;

        }


        .menu-price {

            color: #8b7567;

            font-size: 13px;

            margin-top: 4px;

        }


        .menu-right {

            text-align: right;

            white-space: nowrap;

        }


        .menu-quantity {

            font-size: 13px;

            color: #9b8679;

            margin-bottom: 4px;

        }


        .menu-subtotal {

            font-weight: 700;

            color: #6b3e23;

        }


        /* =========================
           CATATAN
        ========================== */

        .note-box {

            margin-top: 25px;

            padding: 16px 18px;

            border-radius: 14px;

            background: #faf3eb;

        }


        .note-box span {

            display: block;

            color: #9b8679;

            font-size: 12px;

            margin-bottom: 6px;

        }


        .note-box strong {

            color: #5a3422;

            font-size: 14px;

        }


        /* =========================
           TOTAL
        ========================== */

        .total-box {

            margin-top: 25px;

            padding-top: 20px;

            border-top:
                1px solid #d9c6b6;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }


        .total-box span {

            font-size: 14px;

            color: #8b7567;

        }


        .total-box strong {

            font-size: 25px;

            color: #633b27;

        }


        /* =========================
           BACK BUTTON
        ========================== */

        .bottom-button {

            margin-top: 25px;

            display: inline-block;

            padding: 12px 22px;

            border-radius: 25px;

            background: #673e28;

            color: white;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;

            transition: .2s;

        }


        .bottom-button:hover {

            background: #7c4d31;

            transform:
                translateY(-1px);

        }


        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 700px) {

            .navbar {

                padding: 0 20px;

            }


            .navbar nav {

                gap: 12px;

            }


            .navbar nav a {

                font-size: 12px;

            }


            .logo {

                font-size: 25px;

            }


            .detail-heading h1 {

                font-size: 32px;

            }


            .order-header {

                align-items: flex-start;

                flex-direction: column;

            }


            .order-info {

                grid-template-columns: 1fr;

            }


            .menu-item {

                align-items: flex-start;

            }

        }

    </style>

</head>


<body>


    <!-- =========================
         NAVBAR
    ========================== -->

    <header class="navbar">

        <div class="logo">

            COFNA<span>.</span>

        </div>


        <nav>

            <a href="home_cofna.php">
                🏠 Home
            </a>

            <a href="../menu.php">
                ☕ Menu
            </a>

            <a href="keranjang_cofna.php">
                🛒 Keranjang
            </a>

            <a href="riwayat_pesanan.php">
                📋 Pesanan
            </a>

        </nav>

    </header>



    <!-- =========================
         CONTENT
    ========================== -->

    <main class="detail-page">


        <a
            href="riwayat_pesanan.php"
            class="back-link"
        >
            ← Kembali ke Riwayat Pesanan
        </a>



        <div class="detail-heading">

            <span>
                COFNA ORDER DETAIL
            </span>

            <h1>
                Detail
                <em>Pesanan</em>
            </h1>

            <p>
                Berikut detail pesanan kamu
                di COFNA.
            </p>

        </div>



        <div class="detail-card">


            <!-- =========================
                 HEADER PESANAN
            ========================== -->

            <div class="order-header">

                <div>

                    <span class="order-label">
                        NOMOR PESANAN
                    </span>

                    <div class="order-number">

                        <?= htmlspecialchars(
                            $pesanan[
                                'nomor_pesanan'
                            ]
                        ); ?>

                    </div>

                </div>


                <span
                    class="order-status <?= $status_class; ?>"
                >

                    <?= htmlspecialchars(
                        $status
                    ); ?>

                </span>

            </div>



            <!-- =========================
                 INFO PESANAN
            ========================== -->

            <div class="order-info">


                <div class="info-item">

                    <span>
                        👤 Nama Pemesan
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $pesanan[
                                'nama_pemesan'
                            ]
                        ); ?>

                    </strong>

                </div>


                <div class="info-item">

                    <span>
                        📞 Nomor Telepon
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $pesanan[
                                'telepon'
                            ]
                        ); ?>

                    </strong>

                </div>


                <div class="info-item">

                    <span>
                        💳 Pembayaran
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $pesanan[
                                'metode_pembayaran'
                            ]
                        ); ?>

                    </strong>

                </div>


                <div class="info-item">

                    <span>
                        📅 Tanggal Pesanan
                    </span>

                    <strong>

                        <?= date(
                            'd M Y, H:i',
                            strtotime(
                                $pesanan[
                                    'tanggal_pesanan'
                                ]
                            )
                        ); ?>

                    </strong>

                </div>


            </div>



            <!-- =========================
                 DAFTAR MENU
            ========================== -->

            <h2 class="menu-title">
                ☕ Pesanan Kamu
            </h2>


            <?php while (
                $item =
                mysqli_fetch_assoc(
                    $resultDetail
                )
            ): ?>


                <div class="menu-item">


                    <div>

                        <div class="menu-name">

                            <?= htmlspecialchars(
                                $item[
                                    'nama_menu'
                                ]
                            ); ?>

                        </div>


                        <div class="menu-price">

                            Rp<?= number_format(
                                $item['harga'],
                                0,
                                ',',
                                '.'
                            ); ?>

                            / item

                        </div>

                    </div>


                    <div class="menu-right">

                        <div class="menu-quantity">

                            x<?= $item[
                                'jumlah'
                            ]; ?>

                        </div>


                        <div class="menu-subtotal">

                            Rp<?= number_format(
                                $item['subtotal'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </div>

                    </div>


                </div>


            <?php endwhile; ?>



            <!-- =========================
                 CATATAN
            ========================== -->

            <?php if (
                !empty(
                    trim(
                        $pesanan['catatan']
                    )
                )
            ): ?>


                <div class="note-box">

                    <span>
                        📝 Catatan Pesanan
                    </span>

                    <strong>

                        <?= htmlspecialchars(
                            $pesanan[
                                'catatan'
                            ]
                        ); ?>

                    </strong>

                </div>


            <?php endif; ?>



            <!-- =========================
                 TOTAL
            ========================== -->

            <div class="total-box">

                <span>
                    Total Pembayaran
                </span>

                <strong>

                    Rp<?= number_format(
                        $pesanan['total'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </strong>

            </div>


            <a
                href="riwayat_pesanan.php"
                class="bottom-button"
            >
                ← Kembali ke Riwayat
            </a>


        </div>

    </main>


</body>

</html>