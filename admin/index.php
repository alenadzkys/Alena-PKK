<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['hak_akses'] !== 'admin') {
    header("Location: ../html_cofna/login.php");
    exit;
}

require_once "../config/database.php";

/** @var mysqli $conn */

// Ambil nama admin
$nama_admin = $_SESSION['nama'] ?? 'Admin';

// =========================
// HITUNG TOTAL MENU
// =========================
$query_menu = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_menu");

if (!$query_menu) {
    die("Error query total menu: " . mysqli_error($conn));
}

$data_menu = mysqli_fetch_assoc($query_menu);
$total_menu = $data_menu['total'] ?? 0;


// =========================
// HITUNG TOTAL KATEGORI
// =========================
$query_kategori = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT kategori) AS total FROM tb_menu"
);

if (!$query_kategori) {
    die("Error query total kategori: " . mysqli_error($conn));
}

$data_kategori = mysqli_fetch_assoc($query_kategori);
$total_kategori = $data_kategori['total'] ?? 0;


// =========================
// HITUNG TOTAL ADMIN
// =========================
$query_admin = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM tb_user WHERE hak_akses = 'admin'"
);

if (!$query_admin) {
    die("Error query total admin: " . mysqli_error($conn));
}

$data_admin = mysqli_fetch_assoc($query_admin);
$total_admin = $data_admin['total'] ?? 0;


// =========================
// TANGGAL HARI INI
// =========================
$tanggal_hari_ini = date("d M Y");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin COFNA</title>

    <!-- GOOGLE FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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
            background: #f8f0e6;
            color: #4a2d1b;
        }

        a {
            text-decoration: none;
        }


        /* =========================================
           LAYOUT
        ========================================= */

        .dashboard {
            display: flex;
            min-height: 100vh;
        }


        /* =========================================
           SIDEBAR
        ========================================= */

        .sidebar {
            width: 285px;
            min-height: 100vh;

            background: linear-gradient(
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

            z-index: 10;
        }


        .sidebar::before {
            content: "";

            position: absolute;

            width: 180px;
            height: 180px;

            border: 1px solid rgba(255,255,255,.12);

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


        /* =========================================
           BRAND / LOGO
        ========================================= */

        .brand {
            text-align: center;

            margin-bottom: 55px;

            position: relative;

            z-index: 2;
        }


        .brand-logo {
            width: 92px;
            height: 92px;

            background: #f8e8d4;

            border-radius: 50%;

            display: flex;

            align-items: center;
            justify-content: center;

            overflow: hidden;

            margin: 0 auto 18px;
        }


        .brand-logo img {
            width: 100%;
            height: 100%;

            object-fit: contain;

            display: block;
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


        /* =========================================
           SIDEBAR MENU
        ========================================= */

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

            position: relative;

            z-index: 2;
        }


        .side-menu a:hover {
            background: rgba(255,255,255,.10);

            transform: translateX(4px);
        }


        .side-menu a.active {
            background: #a96f4c;

            box-shadow: 0 8px 20px rgba(0,0,0,.12);
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


        /* =========================================
           MAIN
        ========================================= */

        .main {
            margin-left: 285px;

            width: calc(100% - 285px);

            padding: 32px 38px 50px;

            position: relative;

            z-index: 1;
        }


        /* =========================================
           TOP BAR
        ========================================= */

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

            box-shadow: 0 8px 25px rgba(95,60,40,.08);
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


        /* =========================================
           HERO
        ========================================= */

        .hero {
            min-height: 320px;

            border-radius: 30px;

            background:
                radial-gradient(
                    circle at 10% 20%,
                    #fff8f0 0,
                    transparent 25%
                ),
                linear-gradient(
                    135deg,
                    #fffaf5,
                    #f2dfcc
                );

            position: relative;

            overflow: hidden;

            padding: 48px;

            display: flex;

            align-items: center;

            box-shadow: 0 15px 35px rgba(91,55,35,.08);
        }


        .hero::before {
            content: "";

            position: absolute;

            width: 280px;
            height: 280px;

            border-radius: 50%;

            background: #e9c6a7;

            opacity: .22;

            right: 240px;

            top: -120px;
        }


        .hero::after {
            content: "♡";

            position: absolute;

            font-size: 120px;

            color: #b47c59;

            opacity: .12;

            right: 430px;

            bottom: -30px;
        }


        .hero-content {
            position: relative;

            z-index: 3;

            max-width: 55%;
        }


        .welcome-small {
            font-family: 'Playfair Display', serif;

            font-size: 23px;

            color: #805337;

            margin-bottom: 8px;
        }


        .hero h2 {
            font-family: 'Playfair Display', serif;

            font-size: 48px;

            line-height: 1.15;

            color: #4b291b;

            margin-bottom: 15px;
        }


        .hero h2 span {
            color: #9b6140;
        }


        .hero p {
            color: #765f50;

            font-size: 16px;

            line-height: 1.7;

            max-width: 560px;
        }


        .hero-button {
            display: inline-flex;

            align-items: center;

            gap: 10px;

            background: #673e28;

            color: white;

            padding: 13px 22px;

            border-radius: 30px;

            margin-top: 24px;

            font-size: 14px;

            transition: .25s;

            box-shadow: 0 8px 18px rgba(103,62,40,.2);
        }


        .hero-button:hover {
            transform: translateY(-3px);

            background: #7c4d31;
        }


        /* =========================================
           HERO LOGO
        ========================================= */

        .hero-logo-area {
            position: absolute;

            right: 50px;

            top: 50%;

            transform: translateY(-50%);

            width: 340px;

            height: 270px;

            display: flex;

            align-items: center;

            justify-content: center;

            z-index: 2;
        }


        .hero-logo-circle {
            width: 230px;
            height: 230px;

            border-radius: 50%;

            background: #633b27;

            border: 10px solid rgba(255,255,255,.75);

            box-shadow: 0 15px 35px rgba(80,45,25,.2);

            display: flex;

            align-items: center;

            justify-content: center;

            flex-direction: column;

            color: white;

            text-align: center;
        }


        .hero-logo-circle img {
            width: 150px;
            height: 150px;

            object-fit: contain;

            border-radius: 50%;
        }


        .logo-fallback {
            font-size: 70px;
        }


        .hero-logo-circle strong {
            font-family: 'Playfair Display', serif;

            font-size: 24px;

            letter-spacing: 1px;

            margin-top: 3px;
        }


        /* =========================================
           STATISTICS
        ========================================= */

        .stats {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 18px;

            margin-top: 24px;
        }


        .stat-card {
            background: rgba(255,255,255,.82);

            border-radius: 23px;

            padding: 22px;

            min-height: 145px;

            box-shadow: 0 10px 25px rgba(91,55,35,.06);

            position: relative;

            overflow: hidden;
        }


        .stat-card::after {
            content: "☘";

            position: absolute;

            right: 15px;

            bottom: -8px;

            font-size: 45px;

            opacity: .10;
        }


        .stat-top {
            display: flex;

            align-items: center;

            gap: 13px;
        }


        .stat-icon {
            width: 48px;
            height: 48px;

            border-radius: 50%;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 23px;

            background: #f1dfcb;
        }


        .stat-card:nth-child(2) .stat-icon {
            background: #e3e8d0;
        }


        .stat-card:nth-child(3) .stat-icon {
            background: #f2d8d2;
        }


        .stat-card:nth-child(4) .stat-icon {
            background: #e4dced;
        }


        .stat-label {
            color: #806c5e;

            font-size: 14px;
        }


        .stat-number {
            font-family: 'Playfair Display', serif;

            font-size: 30px;

            font-weight: bold;

            margin-top: 8px;
        }


        .stat-description {
            font-size: 12px;

            color: #9a8677;

            margin-top: 5px;
        }


        /* =========================================
           QUICK MENU
        ========================================= */

        .section-title {
            margin-top: 36px;

            margin-bottom: 17px;
        }


        .section-title h3 {
            font-family: 'Playfair Display', serif;

            font-size: 25px;
        }


        .section-title p {
            color: #8b7666;

            font-size: 14px;

            margin-top: 4px;
        }


        .quick-menu {
            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 18px;
        }


        .quick-card {
            min-height: 105px;

            border-radius: 22px;

            padding: 22px;

            background: #f0ddcb;

            display: flex;

            align-items: center;

            gap: 16px;

            color: #4d2c1b;

            transition: .25s;

            position: relative;

            overflow: hidden;
        }


        .quick-card:hover {
            transform: translateY(-5px);

            box-shadow: 0 12px 25px rgba(91,55,35,.12);
        }


        .quick-card.main-card {
            background: #673e28;

            color: white;
        }


        .quick-card:nth-child(3) {
            background: #f5e9dc;
        }


        .quick-icon {
            width: 52px;
            height: 52px;

            min-width: 52px;

            border-radius: 50%;

            display: flex;

            align-items: center;
            justify-content: center;

            background: rgba(255,255,255,.65);

            font-size: 24px;
        }


        .quick-card.main-card .quick-icon {
            background: #a86d48;
        }


        .quick-text h4 {
            font-size: 16px;

            margin-bottom: 5px;
        }


        .quick-text p {
            font-size: 12px;

            opacity: .72;
        }


        .quick-arrow {
            margin-left: auto;

            font-size: 22px;
        }


        /* =========================================
           QUOTE
        ========================================= */

        .quote {
            margin-top: 35px;

            background: linear-gradient(
                120deg,
                #f3dfcb,
                #f8eadd
            );

            border-radius: 24px;

            padding: 25px 35px;

            display: flex;

            justify-content: center;

            align-items: center;

            text-align: center;

            position: relative;

            overflow: hidden;
        }


        .quote p {
            font-family: 'Playfair Display', serif;

            font-size: 19px;

            color: #74472e;

            font-style: italic;
        }


        .quote span {
            display: block;

            margin-top: 5px;

            font-size: 13px;

            color: #9a7965;

            font-family: 'DM Sans', sans-serif;

            font-style: normal;
        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .quick-menu {
                grid-template-columns: 1fr;
            }

            .hero-content {
                max-width: 65%;
            }

            .hero-logo-area {
                right: 0;

                opacity: .9;
            }
        }


        @media (max-width: 800px) {

            .sidebar {
                width: 220px;
            }

            .main {
                margin-left: 220px;

                width: calc(100% - 220px);

                padding: 25px;
            }

            .hero {
                padding: 30px;
            }

            .hero h2 {
                font-size: 36px;
            }

            .hero-logo-area {
                opacity: .25;
            }

            .hero-content {
                max-width: 100%;
            }
        }

    </style>

</head>


<body>

<div class="dashboard">


    <!-- =========================================
         SIDEBAR
    ========================================= -->

    <aside class="sidebar">


        <!-- LOGO -->

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


        <!-- MENU -->

        <div class="menu-title">
            Menu
        </div>


        <div class="side-menu">


            <a href="index.php" class="active">

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


            <a href="kelola_pesanan.php">

                <span class="side-icon">🧾</span>

                <span>Kelola Pesanan</span>

            </a>


        </div>


        <!-- PEMISAH -->

        <div class="divider"></div>


        <!-- MENU BAWAH -->

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


    <!-- =========================================
         MAIN CONTENT
    ========================================= -->

    <main class="main">


        <!-- TOP BAR -->

        <div class="topbar">

            <div class="admin-profile">

                <div class="admin-avatar">
                    👤
                </div>


                <div>

                    <strong>
                        <?= htmlspecialchars($nama_admin); ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </div>


        <!-- =========================================
             HERO
        ========================================= -->

        <section class="hero">


            <div class="hero-content">


                <div class="welcome-small">
                    Selamat Datang,
                </div>


                <h2>
                    Admin <span>COFNA!</span>
                </h2>


                <p>
                    Kelola menu, tambahkan menu baru, dan pantau
                    website COFNA dengan mudah melalui halaman admin.
                </p>


                <a
                    href="kelola_menu.php"
                    class="hero-button"
                >
                    ☕ Kelola Menu
                    <span>→</span>
                </a>


            </div>


            <!-- LOGO COFNA -->

            <div class="hero-logo-area">


                <div class="hero-logo-circle">


                    <img
                        src="../image_cofna/cofna_logo.png"
                        alt="Logo COFNA"
                        onerror="this.style.display='none'; document.getElementById('logoFallback').style.display='block';"
                    >


                    <div
                        id="logoFallback"
                        class="logo-fallback"
                        style="display:none;"
                    >
                        ☕
                    </div>


                </div>


            </div>


        </section>


        <!-- =========================================
             STATISTICS
        ========================================= -->

        <section class="stats">


            <!-- TOTAL MENU -->

            <div class="stat-card">

                <div class="stat-top">

                    <div class="stat-icon">
                        ☕
                    </div>


                    <div>

                        <div class="stat-label">
                            Total Menu
                        </div>


                        <div class="stat-number">
                            <?= $total_menu; ?>
                        </div>

                    </div>

                </div>


                <div class="stat-description">
                    Menu tersedia
                </div>

            </div>


            <!-- TOTAL KATEGORI -->

            <div class="stat-card">

                <div class="stat-top">

                    <div class="stat-icon">
                        📋
                    </div>


                    <div>

                        <div class="stat-label">
                            Total Kategori
                        </div>


                        <div class="stat-number">
                            <?= $total_kategori; ?>
                        </div>

                    </div>

                </div>


                <div class="stat-description">
                    Coffee, Non-Coffee, Favorite
                </div>

            </div>


            <!-- TOTAL ADMIN -->

            <div class="stat-card">

                <div class="stat-top">

                    <div class="stat-icon">
                        👤
                    </div>


                    <div>

                        <div class="stat-label">
                            Admin
                        </div>


                        <div class="stat-number">
                            <?= $total_admin; ?>
                        </div>

                    </div>

                </div>


                <div class="stat-description">
                    Pengguna admin aktif
                </div>

            </div>


            <!-- TANGGAL -->

            <div class="stat-card">

                <div class="stat-top">

                    <div class="stat-icon">
                        📅
                    </div>


                    <div>

                        <div class="stat-label">
                            Hari Ini
                        </div>


                        <div
                            class="stat-number"
                            style="font-size:20px;"
                        >
                            <?= $tanggal_hari_ini; ?>
                        </div>

                    </div>

                </div>


                <div class="stat-description">
                    Website berjalan normal
                </div>

            </div>


        </section>


        <!-- =========================================
             MENU CEPAT
        ========================================= -->

        <div class="section-title">

            <h3>
                ✨ Menu Cepat
            </h3>


            <p>
                Akses fitur penting COFNA dengan cepat
            </p>

        </div>


        <section class="quick-menu">


            <!-- KELOLA MENU -->

            <a
                href="kelola_menu.php"
                class="quick-card main-card"
            >

                <div class="quick-icon">
                    ☕
                </div>


                <div class="quick-text">

                    <h4>
                        Kelola Menu
                    </h4>


                    <p>
                        Edit, hapus, dan atur menu
                    </p>

                </div>


                <div class="quick-arrow">
                    →
                </div>

            </a>


            <!-- TAMBAH MENU -->

            <a
                href="tambah_menu.php"
                class="quick-card"
            >

                <div class="quick-icon">
                    ＋
                </div>


                <div class="quick-text">

                    <h4>
                        Tambah Menu
                    </h4>


                    <p>
                        Tambahkan menu baru
                    </p>

                </div>


                <div class="quick-arrow">
                    →
                </div>

            </a>


            <!-- LIHAT MENU -->

            <a
                href="../menu.php"
                class="quick-card"
            >

                <div class="quick-icon">
                    👁️
                </div>


                <div class="quick-text">

                    <h4>
                        Lihat Menu
                    </h4>


                    <p>
                        Lihat menu di website
                    </p>

                </div>


                <div class="quick-arrow">
                    →
                </div>

            </a>


        </section>


        <!-- =========================================
             QUOTE
        ========================================= -->

        <div class="quote">


            <div>

                <p>
                    "Good Coffee, Good Mood ♡"
                </p>


                <span>
                    Terima kasih telah menjadi bagian dari perjalanan COFNA ☕
                </span>

            </div>


        </div>


    </main>


</div>


</body>

</html>