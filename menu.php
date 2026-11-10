<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config/database.php";

$idUserLogin = isset($_SESSION['id_user'])
    ? (int) $_SESSION['id_user']
    : 0;

$loginSebagaiUser = (
    $idUserLogin > 0 &&
    ($_SESSION['hak_akses'] ?? '') !== 'admin'
);

$query = "SELECT * FROM tb_menu ORDER BY id_menu ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Gagal mengambil data menu: " . mysqli_error($conn));
}

$favoriteMenus = [
    "Caramel Latte",
    "Kopi Susu",
    "Matcha Latte",
    "Strawberry Milk",
    "Cookies & Cream"
];
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Menu - COFNA</title>

    <link
        rel="stylesheet"
        href="css_cofna/cofna.css"
    >

</head>


<body>


<!-- ================= NAVBAR ================= -->


<!-- ================= NAVBAR ================= -->

<header class="navbar">

    <div class="logo">
        COFNA<span>.</span>
    </div>

    <!-- NAVIGASI -->
    <nav class="nav-menu">

        <a href="html_cofna/home_cofna.php">
            🏠 Home
        </a>

        <a href="menu.php" class="nav-active">
            ☕ Menu
        </a>

        <a href="html_cofna/keranjang_cofna.php">
            🛒 Keranjang
            <span id="cart-count" class="cart-count">0</span>
        </a>

        <a href="html_cofna/tentang_kami.php">
            👥 Tentang Kami
        </a>

        <?php if (
            isset($_SESSION['id_user']) &&
            ($_SESSION['hak_akses'] ?? '') === 'user'
        ): ?>

            <a href="html_cofna/riwayat_pesanan.php">
                📋 Riwayat Pesanan
            </a>

        <?php endif; ?>

    </nav>

    <!-- LOGIN / LOGOUT: DI LUAR NAV -->
    <div class="nav-auth">

        <?php if (isset($_SESSION['id_user'])): ?>

            <a href="html_cofna/logout.php" class="login-btn">
                🚪 Logout
            </a>

        <?php else: ?>

            <a href="html_cofna/daftar.php" class="login-btn">
                👤 Login
            </a>

        <?php endif; ?>

    </div>

</header>

<!-- ================= MENU BANNER ================= -->

<section class="menu-banner">

    <div class="menu-banner-overlay"></div>

    <div class="menu-banner-content">

        <span>
            ✦ COFNA MENU
        </span>

        <h1>
            Find Your
            <em>Favorite</em> Drink
        </h1>

    </div>

</section>


<!-- ================= MENU ================= -->

<main class="menu-container">


    <!-- ================= CATEGORY ================= -->

    <div class="category-wrapper">

        <button
            class="category-btn active"
            onclick="filterMenu('all', this)"
        >
            ▦ &nbsp; Semua
        </button>


        <button
            class="category-btn"
            onclick="filterMenu('coffee', this)"
        >
            ☕ &nbsp; Coffee
        </button>


        <button
            class="category-btn"
            onclick="filterMenu('non-coffee', this)"
        >
            🥤 &nbsp; Non-Coffee
        </button>


        <button
            class="category-btn"
            onclick="filterMenu('favorite', this)"
        >
            ♡ &nbsp; Favorite
        </button>

    </div>


    <!-- ================= MENU GRID ================= -->

    <div class="menu-grid">


        <?php if (mysqli_num_rows($result) > 0): ?>


            <?php while ($menu = mysqli_fetch_assoc($result)): ?>


                <?php

                /* Ambil data database */

                $id_menu = (int) $menu['id_menu'];
                
                $nama = $menu['nama_menu'];

                $kategori =
                    strtolower(
                        trim($menu['kategori'])
                    );

                $harga =
                    (int) $menu['harga'];

                $gambar =
                    $menu['gambar'];

                $deskripsi =
                    $menu['deskripsi'];


                /* Tentukan kategori */

                if (
                    $kategori === "kopi" ||
                    $kategori === "coffee"
                ) {

                    $kategoriClass = "coffee";

                } else {

                    $kategoriClass = "non-coffee";

                }


                /* Cek favorite */

                $isFavorite =
                    in_array(
                        $nama,
                        $favoriteMenus,
                        true
                    );


                $favoriteClass =
                    $isFavorite
                    ? " favorite"
                    : "";


                $favoriteIcon =
                    $isFavorite
                    ? "♥"
                    : "♡";

                ?>


                <!-- ================= MENU CARD ================= -->

                <div
                    class="menu-card
                    <?= $kategoriClass . $favoriteClass ?>"
                    data-name="<?= htmlspecialchars($nama) ?>"
                >


                    <!-- GAMBAR -->

                    <div class="menu-image">

                        <img
                            src="image_cofna/<?= htmlspecialchars($gambar) ?>"
                            alt="<?= htmlspecialchars($nama) ?>"
                        >

                    </div>


                    <!-- INFO -->

                    <div class="menu-info">


                        <div class="menu-title">

                            <h3>
                                <?= htmlspecialchars($nama) ?>
                            </h3>


                            <button
                                class="favorite-btn <?= $isFavorite ? 'active' : '' ?>"
                                onclick="toggleFavorite(this)"
                            >
                                <?= $favoriteIcon ?>
                            </button>

                        </div>


                        <p>
                            <?= htmlspecialchars($deskripsi) ?>
                        </p>


                        <div class="menu-bottom">


                            <strong>
                                Rp<?= number_format(
                                    $harga,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </strong>


                            <!--
                                GAMBAR SEKARANG IKUT
                                DISIMPAN KE KERANJANG
                            -->

                           <button 
                           class="add-btn" 
                           onclick='addToCart( 
                           <?= $id_menu ?>, 
                           <?= json_encode($nama) ?>,
                           <?= $harga ?>,
                           <?= json_encode($gambar) ?> 
                           
                        )'
                >
    +
</button>

                        </div>

                    </div>

                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <p>
                Belum ada menu di database.
            </p>


        <?php endif; ?>


    </div>

</main>


<!-- ================= FOOTER ================= -->

<footer class="cofna-footer">


    <div class="footer-main">


        <!-- BRAND -->

        <div class="footer-brand">

            <h2>
                COFNA<span>.</span>
            </h2>

            <p>
                Pesan Dulu, Nikmati Tanpa Antre.
            </p>


            <div class="footer-social">

                <a href="#" aria-label="Instagram">
                    ◎
                </a>

                <a href="#" aria-label="TikTok">
                    ♪
                </a>

                <a href="#" aria-label="WhatsApp">
                    ◉
                </a>

            </div>

        </div>


        <!-- MENU -->

        <div class="footer-column">

            <h3>
                Menu
            </h3>

            <a href="html_cofna/home_cofna.html">
                Home
            </a>

            <a href="menu.php">
                Menu
            </a>

            <a href="html_cofna/keranjang_cofna.html">
                Keranjang
            </a>

        </div>


        <!-- INFORMASI -->

        <div class="footer-column">

            <h3>
                Informasi
            </h3>

            <a href="#">
                Tentang Kami
            </a>

            <a href="#">
                Syarat & Ketentuan
            </a>

            <a href="#">
                Kebijakan Privasi
            </a>

        </div>


        <!-- KONTAK -->

        <div class="footer-column">

            <h3>
                Kontak
            </h3>

            <p>
                hello@cofna.id
            </p>

            <p>
                +62 812-3456-7890
            </p>

            <p>
                Jakarta, Indonesia
            </p>

        </div>


    </div>


    <!-- COPYRIGHT -->

    <div class="footer-bottom">

        <p>
            © 2026 COFNA. All Rights Reserved.
        </p>

        <p>
            Made with ♡ by COFNA
        </p>

    </div>


</footer>


<!-- ================= JAVASCRIPT ================= -->

<script>
    window.COFNA_USER_ID = <?= $idUserLogin ?>;
</script>

<script src="js_cofna/cofna.js"></script>


</body>

</html>