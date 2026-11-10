<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idUserLogin = isset($_SESSION['id_user'])
    ? (int) $_SESSION['id_user']
    : 0;

$sudahLogin = $idUserLogin > 0;
$hakAkses = $_SESSION['hak_akses'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - COFNA</title>
    <link rel="stylesheet" href="../css_cofna/cofna.css">
</head>

<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="logo">COFNA<span>.</span></div>

        <nav class="nav-menu">
            <a href="home_cofna.php">🏠 Home</a>
            <a href="../menu.php">☕ Menu</a>

            <a href="keranjang_cofna.php" class="nav-active">
                🛒 Keranjang <span id="cart-count">0</span>
            </a>

            <a href="tentang_kami.php">👥 Tentang Kami</a>

            <?php if ($sudahLogin && $hakAkses === 'user'): ?>
                <a href="riwayat_pesanan.php">📋 Riwayat Pesanan</a>
            <?php endif; ?>
        </nav>

        <div class="nav-auth">
            <?php if ($sudahLogin): ?>
                <a href="logout.php" class="login-btn">🚪 Logout</a>
            <?php else: ?>
                <a href="login.php" class="login-btn">👤 Login</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- HEADER KERANJANG -->
    <section class="cart-header">
        <div>
            <span>✦ COFNA CART</span>
            <h1>Keranjang <em>Pesananmu</em></h1>
            <p>Periksa kembali pesananmu sebelum melakukan checkout.</p>
        </div>
    </section>

    <!-- ISI KERANJANG -->
    <main class="cart-container">

        <section class="cart-products">
            <div class="cart-title">
                <h2>Pesanan Kamu</h2>
                <span id="cart-total-item">0 item</span>
            </div>

            <div id="cart-items">
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h3>Keranjangmu masih kosong</h3>
                    <p>Yuk pilih minuman favoritmu dulu!</p>
                    <a href="../menu.php" class="btn-primary">
                        Lihat Menu →
                    </a>
                </div>
            </div>
        </section>

        <!-- RINGKASAN PESANAN -->
        <aside class="cart-summary">
            <h2>Ringkasan Pesanan</h2>

            <div class="summary-row">
                <span>Subtotal</span>
                <strong id="subtotal">Rp0</strong>
            </div>

            <div class="summary-row">
                <span>Biaya layanan</span>
                <strong id="service-fee">Rp0</strong>
            </div>

            <div class="summary-line"></div>

            <div class="summary-total">
                <span>Total</span>
                <strong id="cart-grand-total">Rp0</strong>
            </div>

            <button
                type="button"
                class="checkout-btn"
                onclick="checkout()"
            >
                Checkout <span>→</span>
            </button>

            <a href="../menu.php" class="continue-shopping">
                ← Lanjut Belanja
            </a>
        </aside>

    </main>

    <!-- FOOTER -->
    <footer class="cofna-footer">
        <div class="footer-main">

            <div class="footer-brand">
                <h2>COFNA<span>.</span></h2>
                <p>Pesan Dulu, Nikmati Tanpa Antre.</p>

                <div class="footer-social">
                    <a href="#">◎</a>
                    <a href="#">♪</a>
                    <a href="#">◉</a>
                </div>
            </div>

            <div class="footer-column">
                <h3>Menu</h3>
                <a href="home_cofna.php">Home</a>
                <a href="../menu.php">Menu</a>
                <a href="keranjang_cofna.php">Keranjang</a>
            </div>

            <div class="footer-column">
                <h3>Informasi</h3>
                <a href="tentang_kami.php">Tentang Kami</a>
                <a href="#">Syarat &amp; Ketentuan</a>
                <a href="#">Kebijakan Privasi</a>
            </div>

            <div class="footer-column">
                <h3>Kontak</h3>
                <p>hello@cofna.id</p>
                <p>+62 812-3456-7890</p>
                <p>Jakarta, Indonesia</p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© 2026 COFNA. All Rights Reserved.</p>
            <p>Made with ♡ by COFNA</p>
        </div>
    </footer>

    <!-- STATUS LOGIN UNTUK JAVASCRIPT -->
    <script>
        window.COFNA_USER_ID = <?= json_encode($idUserLogin) ?>;
        window.COFNA_IS_LOGGED_IN = <?= json_encode($sudahLogin) ?>;
    </script>

    <script src="../js_cofna/cofna.js?v=4"></script>

</body>
</html>