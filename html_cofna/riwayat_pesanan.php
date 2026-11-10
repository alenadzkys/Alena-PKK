<?php

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

/** @var mysqli $conn */

$id_user = $_SESSION['id_user'];

$query = "
    SELECT
        id_pesanan,
        nomor_pesanan,
        metode_pembayaran,
        total,
        status,
        tanggal_pesanan
    FROM tb_pesanan
    WHERE id_user = ?
    ORDER BY id_pesanan DESC
";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_user
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Riwayat Pesanan - COFNA</title>

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

        .history-page {

            max-width: 1000px;

            margin: auto;

            padding: 55px 25px 80px;

        }


        .page-heading {

            margin-bottom: 35px;

        }


        .page-heading span {

            color: #a96f4c;

            font-size: 12px;

            font-weight: 700;

            letter-spacing: 2px;

        }


        .page-heading h1 {

            margin: 8px 0;

            font-family:
                'Playfair Display',
                serif;

            font-size: 40px;

            color: #4b291b;

        }


        .page-heading p {

            margin: 0;

            color: #8b7567;

        }


        /* =========================
           AUTO UPDATE INFO
        ========================== */

        .live-status {

            display: flex;

            align-items: center;

            gap: 8px;

            margin-bottom: 22px;

            padding: 11px 15px;

            width: fit-content;

            border-radius: 20px;

            background: #f1eadf;

            color: #795238;

            font-size: 12px;

            font-weight: 600;

        }


        .live-dot {

            width: 8px;

            height: 8px;

            border-radius: 50%;

            background: #6b9b63;

            animation: pulse 1.5s infinite;

        }


        @keyframes pulse {

            0% {
                opacity: 1;
            }

            50% {
                opacity: .35;
            }

            100% {
                opacity: 1;
            }

        }


        /* =========================
           ORDER CARD
        ========================== */

        .order-card {

            background: #fffdfb;

            border:
                1px solid #eaded3;

            border-radius: 20px;

            padding: 24px;

            margin-bottom: 18px;

            box-shadow:
                0 8px 25px
                rgba(75, 41, 27, .05);

            transition: .25s;

        }


        .order-card:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 12px 30px
                rgba(75, 41, 27, .08);

        }


        .order-top {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding-bottom: 18px;

            border-bottom:
                1px solid #eee2d8;

        }


        .order-label {

            display: block;

            font-size: 11px;

            color: #9b8679;

            margin-bottom: 5px;

        }


        .order-number {

            font-size: 22px;

            font-weight: 700;

            color: #633b27;

        }


        /* =========================
           STATUS
        ========================== */

        .status {

            padding: 8px 14px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: 700;

            white-space: nowrap;

            transition: .3s;

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
           ORDER INFO
        ========================== */

        .order-info {

            display: flex;

            justify-content: space-between;

            gap: 20px;

            padding: 20px 0;

        }


        .info-item span {

            display: block;

            color: #9b8679;

            font-size: 12px;

            margin-bottom: 5px;

        }


        .info-item strong {

            color: #5a3422;

            font-size: 14px;

        }


        /* =========================
           DATE
        ========================== */

        .order-date {

            color: #9b8679;

            font-size: 12px;

            margin-bottom: 15px;

        }


        /* =========================
           DETAIL BUTTON
        ========================== */

        .detail-button {

            display: inline-block;

            padding: 10px 18px;

            border:
                1px solid #6b3e23;

            border-radius: 22px;

            color: #6b3e23;

            text-decoration: none;

            font-size: 13px;

            font-weight: 600;

            transition: .2s;

        }


        .detail-button:hover {

            background: #6b3e23;

            color: white;

            transform:
                translateY(-1px);

        }


        /* =========================
           EMPTY
        ========================== */

        .empty-state {

            text-align: center;

            padding: 70px 20px;

            background: #fffdfb;

            border:
                1px solid #eaded3;

            border-radius: 20px;

        }


        .empty-icon {

            font-size: 50px;

            margin-bottom: 15px;

        }


        .empty-state h2 {

            margin: 0 0 8px;

            font-family:
                'Playfair Display',
                serif;

        }


        .empty-state p {

            color: #8b7567;

            margin-bottom: 25px;

        }


        .menu-button {

            display: inline-block;

            padding: 12px 22px;

            background: #673e28;

            color: white;

            border-radius: 25px;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;

        }


        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 700px) {

            .navbar {

                padding: 0 20px;

            }


            .logo {

                font-size: 25px;

            }


            .navbar nav {

                gap: 12px;

            }


            .navbar nav a {

                font-size: 12px;

            }


            .page-heading h1 {

                font-size: 32px;

            }


            .order-top {

                align-items: flex-start;

                flex-direction: column;

            }


            .order-info {

                flex-direction: column;

                gap: 15px;

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

    <main class="history-page">


        <div class="page-heading">

            <span>
                COFNA ORDER HISTORY
            </span>

            <h1>
                Riwayat
                <em>Pesanan</em>
            </h1>

            <p>
                Lihat semua pesanan yang pernah
                kamu buat di COFNA.
            </p>

        </div>



        <?php if (mysqli_num_rows($result) > 0): ?>


            <!-- STATUS OTOMATIS -->

            <div class="live-status">

                <span class="live-dot"></span>

                Status pesanan diperbarui otomatis

            </div>



            <!-- =========================
                 LIST PESANAN
            ========================== -->

            <?php while (
                $pesanan =
                mysqli_fetch_assoc($result)
            ): ?>


                <?php

                $status = $pesanan['status'];

                $status_class = "status-process";


                if (
                    $status === "Sedang Dibuat"
                ) {

                    $status_class =
                        "status-making";

                }

                elseif (
                    $status === "Siap Diambil"
                ) {

                    $status_class =
                        "status-ready";

                }

                elseif (
                    $status === "Selesai"
                ) {

                    $status_class =
                        "status-done";

                }

                ?>


                <div
                    class="order-card"
                    data-order-number="<?= htmlspecialchars(
                        $pesanan['nomor_pesanan']
                    ); ?>"
                >


                    <!-- TOP -->

                    <div class="order-top">


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
                            class="status <?= $status_class; ?>"
                        >

                            <?= htmlspecialchars(
                                $status
                            ); ?>

                        </span>


                    </div>



                    <!-- INFO -->

                    <div class="order-info">


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
                                💰 Total
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


                    </div>



                    <!-- DATE -->

                    <div class="order-date">

                        📅 Pesanan dibuat:

                        <?= date(
                            'd M Y, H:i',
                            strtotime(
                                $pesanan[
                                    'tanggal_pesanan'
                                ]
                            )
                        ); ?>

                    </div>



                    <!-- DETAIL -->

                    <a
                        href="detail_riwayat.php?id=<?= $pesanan['id_pesanan']; ?>"
                        class="detail-button"
                    >
                        👁️ Lihat Detail
                    </a>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <!-- EMPTY -->

            <div class="empty-state">

                <div class="empty-icon">
                    ☕
                </div>


                <h2>
                    Belum Ada Pesanan
                </h2>


                <p>
                    Kamu belum mempunyai
                    riwayat pesanan di COFNA.
                </p>


                <a
                    href="../menu.php"
                    class="menu-button"
                >
                    Pesan Sekarang
                </a>

            </div>


        <?php endif; ?>


    </main>



    <!-- =========================
         AUTO UPDATE SCRIPT
    ========================== -->

    <script>

        function getStatusClass(status) {

            if (
                status === "Sedang Dibuat"
            ) {

                return "status-making";

            }

            if (
                status === "Siap Diambil"
            ) {

                return "status-ready";

            }

            if (
                status === "Selesai"
            ) {

                return "status-done";

            }

            return "status-process";

        }


        async function updateOrderStatuses() {

            const cards =
                document.querySelectorAll(
                    ".order-card"
                );


            if (cards.length === 0) {

                return;

            }


            for (const card of cards) {


                const nomorPesanan =
                    card.dataset.orderNumber;


                if (!nomorPesanan) {

                    continue;

                }


                try {


                    const response =
                        await fetch(
                            "cek_status_pesanan.php?nomor_pesanan="
                            +
                            encodeURIComponent(
                                nomorPesanan
                            )
                            +
                            "&t="
                            +
                            Date.now()
                        );


                    const data =
                        await response.json();


                    if (
                        !data.success ||
                        !data.order
                    ) {

                        continue;

                    }


                    const status =
                        data.order.status;


                    const statusElement =
                        card.querySelector(
                            ".status"
                        );


                    if (!statusElement) {

                        continue;

                    }


                    const newClass =
                        getStatusClass(
                            status
                        );


                    /*
                     * Hapus class status lama
                     */

                    statusElement.classList.remove(
                        "status-process",
                        "status-making",
                        "status-ready",
                        "status-done"
                    );


                    /*
                     * Tambahkan class baru
                     */

                    statusElement.classList.add(
                        newClass
                    );


                    /*
                     * Update tulisan status
                     */

                    statusElement.textContent =
                        status;


                } catch (error) {

                    console.log(
                        "Gagal mengecek status:",
                        error
                    );

                }

            }

        }


        /*
         * Cek status pertama kali
         */

        updateOrderStatuses();


        /*
         * Cek setiap 5 detik
         */

        setInterval(
            updateOrderStatuses,
            5000
        );

    </script>


</body>

</html>