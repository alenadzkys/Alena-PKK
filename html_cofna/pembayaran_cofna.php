
<?php
session_start();

if (
    !isset($_SESSION['id_user']) ||
    ($_SESSION['hak_akses'] ?? '') !== 'user'
) {
    header("Location: login.php");
    exit;
}

$idUserLogin = (int) $_SESSION['id_user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - COFNA</title>
    <link rel="stylesheet" href="../css_cofna/cofna.css">
</head>

<body>


<header class="navbar">
    <div class="logo">
        <a href="home_cofna.php">
            <div class="logo">
            COFNA<span>.</span>
        </div>
        </a>
    </div>

    <nav class="nav-menu">
        <a href="home_cofna.php">🏠 Home</a>
        <a href="../menu.php">☕ Menu</a>
        <a href="keranjang_cofna.php">🛒 Keranjang</a>
    </nav>
</header>

<main class="payment-page">
    <div class="payment-container">

        <div class="payment-heading">
            <span>☕ COFNA PAYMENT</span>
            <h1>Pembayaran <em>Pesananmu</em></h1>
            <p>
                Selesaikan pembayaran terlebih dahulu
                sebelum mengambil pesanan.
            </p>
        </div>

        <div class="payment-total">
            <p>Total yang harus dibayar</p>
            <h2 id="payment-total">Rp0</h2>
        </div>

        <section class="payment-method">
            <div id="payment-content"></div>
        </section>

        <div class="payment-notice">
            <span>🔒</span>
            <p>
                Pembayaran dilakukan sebelum pesanan diambil.
                Setelah pembayaran berhasil dicatat, tunjukkan
                nomor pesanan saat mengambil minuman.
            </p>
        </div>

        <button
            type="button"
            class="pay-now-btn"
            id="confirm-payment"
            onclick="processPayment()"
        >
            Saya Sudah Bayar →
        </button>

        <a href="checkout_cofna.php" class="back-payment">
            ← Kembali ke Checkout
        </a>

    </div>
</main>

<script>
window.COFNA_USER_ID = <?= json_encode($idUserLogin) ?>;

function formatRupiahPembayaran(number) {
    return "Rp" + Number(number).toLocaleString("id-ID");
}

function tampilkanPembayaran() {
    const total = localStorage.getItem("cofnaPaymentTotal");
    const checkoutText = localStorage.getItem("cofnaCheckoutData");
    const cartText = localStorage.getItem("cofnaCart");

    const totalElement = document.getElementById("payment-total");
    const paymentContent = document.getElementById("payment-content");
    const button = document.getElementById("confirm-payment");

    let data;
    let cart;

    try {
        data = checkoutText ? JSON.parse(checkoutText) : null;
        cart = cartText ? JSON.parse(cartText) : [];
    } catch (error) {
        data = null;
        cart = [];
    }

    // Validasi data checkout
    if (
        !data ||
        !data.payment ||
        !Array.isArray(cart) ||
        cart.length === 0
    ) {
        totalElement.textContent = "Belum tersedia";

        paymentContent.innerHTML = `
            <div class="payment-error">
                <h2>Data pesanan belum ditemukan</h2>
                <p>Silakan kembali ke checkout dan lanjutkan
                dari keranjang agar total pembayaran dapat ditampilkan.</p>
                <a href="keranjang_cofna.php">Kembali ke Keranjang →</a>
            </div>
        `;

        button.disabled = true;
        return;
    }

    // Hitung ulang total dari isi keranjang
    let subtotal = 0;

    cart.forEach(function(item) {
        subtotal += Number(item.harga || 0) *
                    Number(item.jumlah || 1);
    });

    const serviceFee = 2000;
    const calculatedTotal = subtotal + serviceFee;

    // Pastikan total yang ditampilkan sesuai isi keranjang
    localStorage.setItem(
        "cofnaPaymentTotal",
        String(calculatedTotal)
    );

    data.subtotal = subtotal;
    data.serviceFee = serviceFee;
    data.total = calculatedTotal;

    localStorage.setItem(
        "cofnaCheckoutData",
        JSON.stringify(data)
    );

    totalElement.textContent =
        formatRupiahPembayaran(calculatedTotal);

    const metode = data.payment;

    let icon = "";

    if (metode === "QRIS")
    if (metode === "GoPay")
    if (metode === "DANA") 
    if (metode === "OVO")

    if (!["QRIS", "GoPay", "DANA", "OVO"].includes(metode)) {
        paymentContent.innerHTML = `
            <div class="payment-error">
                <h2>Metode pembayaran tidak dikenali</h2>
                <p>Silakan kembali ke checkout dan pilih metode pembayaran.</p>
            </div>
        `;

        button.disabled = true;
        return;
    }

    paymentContent.innerHTML = `
        <div class="payment-detail">
            <div class="payment-big-icon">${icon}</div>
            <h2>Pembayaran ${metode}</h2>
            <p>
                Gunakan metode ${metode} untuk menyelesaikan
                pembayaran pesananmu.
            </p>

            <div class="payment-qr">
                <img
                    src="../image_cofna/codeqr-cofna.jpeg"
                    alt="QR Code pembayaran COFNA"
                    onerror="this.style.display='none'; document.getElementById('qr-error').style.display='block';"
                >
                <p id="qr-error" style="display:none">
                    Gambar QR tidak ditemukan. Periksa file
                    codeqr-cofna.jpeg di folder image_cofna.
                </p>
            </div>

            <p class="payment-instruction">
                Pastikan QR yang digunakan benar dan nominal
                pembayaran sesuai dengan total pesanan.
            </p>
        </div>
    `;

    button.disabled = false;
}

async function processPayment() {
    const button = document.getElementById("confirm-payment");

    const checkoutText = localStorage.getItem("cofnaCheckoutData");
    const cartText = localStorage.getItem("cofnaCart");

    if (!checkoutText || !cartText) {
        alert("Data pesanan tidak ditemukan. Silakan checkout ulang.");
        return;
    }

    let data;
    let cart;

    try {
        data = JSON.parse(checkoutText);
        cart = JSON.parse(cartText);
    } catch (error) {
        alert("Data pesanan tidak valid. Silakan checkout ulang.");
        return;
    }

    if (!data.payment || !Array.isArray(cart) || cart.length === 0) {
        alert("Metode pembayaran atau keranjang belum valid.");
        return;
    }

    // Hitung ulang total berdasarkan keranjang
    let subtotal = 0;

    cart.forEach(function(item) {
        subtotal += Number(item.harga || 0) *
                    Number(item.jumlah || 1);
    });

    const total = subtotal + 2000;

    button.disabled = true;
    button.textContent = "Memproses Pesanan...";

    try {
        const response = await fetch("proses_pesanan.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                catatan: data.catatan || "",
                payment: data.payment,
                total: total,
                items: cart
            })
        });

        const text = await response.text();

        let result;

        try {
            result = JSON.parse(text);
        } catch (error) {
            console.error("Respons PHP:", text);
            alert("Respons server tidak valid. Periksa proses_pesanan.php.");
            return;
        }

        if (!response.ok || !result.success) {
            alert(result.message || "Pesanan gagal diproses.");
            return;
        }

        const order = {
            nomorPesanan: result.nomor_pesanan,
            id_pesanan: result.id_pesanan,
            catatan: data.catatan || "",
            payment: data.payment,
            total: total,
            status: "Sedang Diproses",
            tanggal: new Date().toLocaleString("id-ID")
        };

        localStorage.setItem("cofnaOrder", JSON.stringify(order));

        localStorage.removeItem("cofnaCart");
        localStorage.removeItem("cofnaPaymentTotal");
        localStorage.removeItem("cofnaCheckoutData");

        window.location.href = "pesanan_berhasil.html";

    } catch (error) {
        console.error("Error pembayaran:", error);
        alert("Gagal terhubung ke server. Coba lagi.");
    } finally {
        button.disabled = false;
        button.textContent = "Saya Sudah Bayar →";
    }
}

document.addEventListener("DOMContentLoaded", tampilkanPembayaran);
</script>

</body>
</html>