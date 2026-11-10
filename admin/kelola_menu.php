<?php

session_start();

require_once "../config/database.php";

/** @var mysqli $conn */

/* =========================
   CEK LOGIN ADMIN
========================= */

if (!isset($_SESSION['id_user'])) {
    header("Location: ../html_cofna/login.php");
    exit;
}

if (($_SESSION['hak_akses'] ?? '') !== 'admin') {
    header("Location: ../html_cofna/home_cofna.php");
    exit;
}


$nama_admin = htmlspecialchars(
    $_SESSION['nama'] ?? 'Admin'
);


/* =========================
   AMBIL DATA MENU
========================= */

$query = mysqli_query(
    $conn,
    "SELECT * FROM tb_menu ORDER BY id_menu DESC"
);

$total_menu = mysqli_num_rows($query);

?>


<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kelola Menu - COFNA Admin</title>


    <!-- GOOGLE FONT -->

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

            font-family:
                'DM Sans',
                sans-serif;

            background: #f8f0e6;

            color: #4a2d1b;
        }


        a {
            text-decoration: none;
        }


        /* =========================
           DASHBOARD
        ========================= */

        .dashboard {

            display: flex;

            min-height: 100vh;
        }


        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {

            width: 285px;

            min-height: 100vh;

            background:
                linear-gradient(
                    180deg,
                    #4b291b,
                    #633b27,
                    #4b291b
                );

            color: white;

            padding: 30px 22px;

            position: fixed;

            left: 0;
            top: 0;
            bottom: 0;

            overflow: hidden;
        }


        .sidebar::before {

            content: "";

            position: absolute;

            width: 180px;
            height: 180px;

            border:
                1px solid
                rgba(255,255,255,.12);

            border-radius: 50%;

            bottom: -60px;
            left: -50px;
        }


        .sidebar::after {

            content: "☕";

            position: absolute;

            font-size: 120px;

            opacity: .04;

            right: -30px;
            top: 170px;
        }


        /* =========================
           BRAND
        ========================= */

        .brand {

            text-align: center;

            margin-bottom: 55px;

            position: relative;

            z-index: 2;
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
    margin: auto;
    box-shadow: 0 10px 25px rgba(0,0,0,.15);
}

.brand-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}


        .brand h1 {

            font-family:
                'Playfair Display',
                serif;

            font-size: 36px;

            letter-spacing: 2px;

            margin-top: 12px;
        }


        .brand p {

            color: #e6c9b1;

            margin-top: 4px;

            font-size: 14px;
        }


        /* =========================
           SIDEBAR MENU
        ========================= */

        .menu-title {

            color: #d5b9a5;

            font-size: 13px;

            margin:
                0 12px 14px;

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

            position: relative;

            z-index: 2;
        }


        .side-menu a:hover {

            background:
                rgba(255,255,255,.10);

            transform:
                translateX(4px);
        }


        .side-menu a.active {

            background: #a96f4c;

            box-shadow:
                0 8px 20px
                rgba(0,0,0,.12);
        }


        .side-icon {

            width: 25px;

            text-align: center;

            font-size: 20px;
        }


        .divider {

            height: 1px;

            background:
                rgba(255,255,255,.18);

            margin: 30px 8px;
        }


        /* =========================
           MAIN
        ========================= */

        .main {

            margin-left: 285px;

            width:
                calc(100% - 285px);

            padding:
                32px 38px 50px;

            min-height: 100vh;
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

            padding:
                10px 18px 10px 10px;

            border-radius: 30px;

            display: flex;

            align-items: center;

            gap: 11px;

            box-shadow:
                0 8px 25px
                rgba(95,60,40,.08);
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
           PAGE HEADER
        ========================= */

        .page-header {

            background:
                linear-gradient(
                    135deg,
                    #fffaf5,
                    #f2dfcc
                );

            border-radius: 30px;

            padding: 38px 42px;

            margin-bottom: 24px;

            box-shadow:
                0 15px 35px
                rgba(91,55,35,.08);

            position: relative;

            overflow: hidden;
        }


        .page-header::after {

            content: "☕";

            position: absolute;

            right: 35px;
            top: -25px;

            font-size: 130px;

            opacity: .06;

            transform: rotate(15deg);
        }


        .page-header-content {

            position: relative;

            z-index: 2;
        }


        .small-title {

            font-family:
                'Playfair Display',
                serif;

            font-size: 20px;

            color: #805337;

            margin-bottom: 5px;
        }


        .page-header h1 {

            font-family:
                'Playfair Display',
                serif;

            font-size: 42px;

            color: #4b291b;

            margin-bottom: 9px;
        }


        .page-header h1 span {

            color: #9b6140;
        }


        .page-header p {

            color: #765f50;

            font-size: 15px;

            line-height: 1.6;
        }


        /* =========================
           TOTAL MENU
        ========================= */

        .menu-summary {

            background: white;

            border-radius: 23px;

            padding: 20px 24px;

            display: flex;

            align-items: center;

            gap: 15px;

            margin-bottom: 30px;

            box-shadow:
                0 10px 25px
                rgba(91,55,35,.06);
        }


        .summary-icon {

            width: 55px;
            height: 55px;

            border-radius: 50%;

            background: #f1dfcb;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 25px;
        }


        .summary-text {

            flex: 1;
        }


        .summary-text p {

            font-size: 13px;

            color: #806c5e;

            margin-bottom: 3px;
        }


        .summary-text h2 {

            font-family:
                'Playfair Display',
                serif;

            font-size: 27px;

            color: #4b291b;
        }


        .add-button {

            background: #673e28;

            color: white;

            padding:
                13px 20px;

            border-radius: 30px;

            font-size: 14px;

            font-weight: 600;

            transition: .25s;

            box-shadow:
                0 8px 18px
                rgba(103,62,40,.18);
        }


        .add-button:hover {

            background: #7c4d31;

            transform:
                translateY(-2px);
        }


        /* =========================
           SECTION TITLE
        ========================= */

        .section-title {

            margin-bottom: 18px;
        }


        .section-title h3 {

            font-family:
                'Playfair Display',
                serif;

            font-size: 27px;

            color: #4b291b;
        }


        .section-title p {

            color: #8b7666;

            font-size: 14px;

            margin-top: 4px;
        }


        /* =========================
           MENU GRID
        ========================= */

        .menu-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 22px;
        }


        /* =========================
           MENU CARD
        ========================= */

        .menu-card {

            background: white;

            border-radius: 25px;

            overflow: hidden;

            box-shadow:
                0 10px 25px
                rgba(91,55,35,.07);

            transition: .25s;

            position: relative;
        }


        .menu-card:hover {

            transform:
                translateY(-6px);

            box-shadow:
                0 18px 35px
                rgba(91,55,35,.12);
        }


        /* =========================
           IMAGE
        ========================= */

        .menu-image {

            width: 100%;

            height: 205px;

            background: #f2e4d6;

            position: relative;

            overflow: hidden;
        }


        .menu-image img {

            width: 100%;
            height: 100%;

            object-fit: cover;

            display: block;

            transition: .3s;
        }


        .menu-card:hover
        .menu-image img {

            transform: scale(1.04);
        }


        .no-image {

            width: 100%;
            height: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 60px;

            color: #a98265;
        }


        /* =========================
           CATEGORY
        ========================= */

        .category {

            position: absolute;

            top: 14px;
            left: 14px;

            background:
                rgba(255,255,255,.92);

            color: #70452e;

            padding:
                7px 12px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 700;

            box-shadow:
                0 5px 12px
                rgba(0,0,0,.08);
        }


        /* =========================
           CARD CONTENT
        ========================= */

        .menu-content {

            padding: 21px;
        }


        .menu-content h4 {

            font-family:
                'Playfair Display',
                serif;

            font-size: 21px;

            color: #4b291b;

            margin-bottom: 7px;
        }


        .menu-description {

            color: #806c5e;

            font-size: 13px;

            line-height: 1.55;

            min-height: 41px;

            margin-bottom: 15px;
        }


        .menu-bottom {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 17px;
        }


        .price {

            font-family:
                'Playfair Display',
                serif;

            font-size: 20px;

            font-weight: 700;

            color: #9b6140;
        }


        /* =========================
           ACTION BUTTON
        ========================= */

        .actions {

            display: flex;

            gap: 8px;
        }


        .btn-edit,
        .btn-delete {

            flex: 1;

            text-align: center;

            padding: 10px;

            border-radius: 12px;

            font-size: 13px;

            font-weight: 600;

            transition: .2s;
        }


        .btn-edit {

            background: #f0dfcf;

            color: #70452e;
        }


        .btn-edit:hover {

            background: #e5cdb8;
        }


        .btn-delete {

            background: #f3dddd;

            color: #9b4f4f;
        }


        .btn-delete:hover {

            background: #eacaca;
        }


        /* =========================
           EMPTY STATE
        ========================= */

        .empty {

            grid-column: 1 / -1;

            background: white;

            border-radius: 25px;

            padding: 60px 30px;

            text-align: center;

            box-shadow:
                0 10px 25px
                rgba(91,55,35,.06);
        }


        .empty-icon {

            font-size: 60px;

            margin-bottom: 15px;
        }


        .empty h3 {

            font-family:
                'Playfair Display',
                serif;

            font-size: 25px;

            margin-bottom: 7px;
        }


        .empty p {

            color: #8b7666;

            font-size: 14px;

            margin-bottom: 20px;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1100px) {

            .menu-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }

        }


        @media (max-width: 800px) {

            .sidebar {

                width: 220px;
            }


            .main {

                margin-left: 220px;

                width:
                    calc(100% - 220px);

                padding: 25px;
            }


            .menu-grid {

                grid-template-columns: 1fr;
            }


            .page-header h1 {

                font-size: 34px;
            }

        }


        @media (max-width: 600px) {

            .dashboard {

                display: block;
            }


            .sidebar {

                position: static;

                width: 100%;

                min-height: auto;
            }


            .main {

                margin-left: 0;

                width: 100%;

                padding: 20px;
            }


            .menu-summary {

                flex-wrap: wrap;
            }


            .add-button {

                width: 100%;

                text-align: center;
            }

        }

    </style>

</head>


<body>


<div class="dashboard">


    <!-- =========================
         SIDEBAR
    ========================= -->

    <aside class="sidebar">

    <div class="brand">

        <div class="brand-logo">
            <img src="../image_cofna/cofna_logo.png" alt="Logo COFNA">
        </div>

        <h1>COFNA</h1>

        <p>Admin Panel</p>

    </div>


    <div class="menu-title">
        Menu
    </div>


    <div class="side-menu">

        <a href="index.php">

            <span class="side-icon">
                🏠
            </span>

            <span>
                Beranda
            </span>

        </a>


        <a href="kelola_menu.php" class="active">

            <span class="side-icon">
                ☕
            </span>

            <span>
                Kelola Menu
            </span>

        </a>


        <a href="tambah_menu.php">

            <span class="side-icon">
                ＋
            </span>

            <span>
                Tambah Menu
            </span>

        </a>

        <a href="kelola_pesanan.php">
            <span class="side-icon">🧾</span>
            <span>Kelola Pesanan</span>
        </a>

    </div>


    <div class="divider"></div>


    <div class="side-menu">

        <a href="../menu.php">

            <span class="side-icon">
                👀
            </span>

            <span>
                Lihat Menu
            </span>

        </a>


        <a href="../html_cofna/home_cofna.php">

            <span class="side-icon">
                🏡
            </span>

            <span>
                Ke Home
            </span>

        </a>


        <a href="../html_cofna/logout.php">

            <span class="side-icon">
                🚪
            </span>

            <span>
                Logout
            </span>

        </a>

    </div>

</aside>



    <!-- =========================
         MAIN
    ========================= -->

    <main class="main">


        <!-- TOPBAR -->

        <div class="topbar">

            <div class="admin-profile">

                <div class="admin-avatar">
                    👤
                </div>

                <div>

                    <strong>
                        <?= $nama_admin; ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </div>



        <!-- PAGE HEADER -->

        <section class="page-header">

            <div class="page-header-content">

                <div class="small-title">
                    Admin COFNA
                </div>

                <h1>
                    Kelola <span>Menu ☕</span>
                </h1>

                <p>
                    Kelola semua menu yang tersedia di COFNA.
                    Kamu bisa menambahkan, mengedit, atau
                    menghapus menu dengan mudah.
                </p>

            </div>

        </section>



        <!-- SUMMARY -->

        <div class="menu-summary">

            <div class="summary-icon">
                ☕
            </div>


            <div class="summary-text">

                <p>
                    Total menu tersedia
                </p>

                <h2>
                    <?= $total_menu; ?> Menu
                </h2>

            </div>


            <a
                href="tambah_menu.php"
                class="add-button"
            >
                ＋ Tambah Menu
            </a>

        </div>



        <!-- SECTION TITLE -->

        <div class="section-title">

            <h3>
                Daftar Menu
            </h3>

            <p>
                Pilih menu yang ingin kamu kelola.
            </p>

        </div>



        <!-- MENU GRID -->

        <div class="menu-grid">


            <?php if ($total_menu > 0): ?>


                <?php while ($menu = mysqli_fetch_assoc($query)): ?>


                    <div class="menu-card">


                        <!-- IMAGE -->

                        <div class="menu-image">


                            <?php

                            $gambar =
                                trim(
                                    $menu['gambar'] ?? ''
                                );

                            $path_gambar =
                                "../image_cofna/" .
                                $gambar;

                            ?>


                            <?php if (
                                !empty($gambar) &&
                                file_exists($path_gambar)
                            ): ?>

                                <img
                                    src="<?= htmlspecialchars($path_gambar); ?>"
                                    alt="<?= htmlspecialchars($menu['nama_menu']); ?>"
                                >

                            <?php else: ?>

                                <div class="no-image">
                                    ☕
                                </div>

                            <?php endif; ?>


                            <!-- CATEGORY -->

                            <div class="category">

                                <?= htmlspecialchars(
                                    $menu['kategori']
                                ); ?>

                            </div>


                        </div>



                        <!-- CONTENT -->

                        <div class="menu-content">


                            <h4>
                                <?= htmlspecialchars(
                                    $menu['nama_menu']
                                ); ?>
                            </h4>


                            <p class="menu-description">

                                <?= htmlspecialchars(
                                    $menu['deskripsi']
                                ); ?>

                            </p>



                            <div class="menu-bottom">

                                <div class="price">

                                    Rp<?= number_format(
                                        $menu['harga'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </div>

                            </div>



                            <!-- ACTION -->

                            <div class="actions">


                                <a
                                    href="edit_menu.php?id=<?= $menu['id_menu']; ?>"
                                    class="btn-edit"
                                >
                                    ✏️ Edit
                                </a>


                                <a
                                    href="hapus_menu.php?id=<?= $menu['id_menu']; ?>"
                                    class="btn-delete"
                                    onclick="return confirm('Yakin ingin menghapus menu ini?');"
                                >
                                    🗑️ Hapus
                                </a>


                            </div>


                        </div>


                    </div>


                <?php endwhile; ?>


            <?php else: ?>


                <div class="empty">

                    <div class="empty-icon">
                        ☕
                    </div>

                    <h3>
                        Belum Ada Menu
                    </h3>

                    <p>
                        Tambahkan menu pertama COFNA
                        untuk mulai mengelola daftar menu.
                    </p>

                    <a
                        href="tambah_menu.php"
                        class="add-button"
                    >
                        ＋ Tambah Menu
                    </a>

                </div>


            <?php endif; ?>


        </div>


    </main>


</div>


</body>

</html>