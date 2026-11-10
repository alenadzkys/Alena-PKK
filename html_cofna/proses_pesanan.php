
<?php
session_start();

header("Content-Type: application/json; charset=utf-8");

require_once "../config/database.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Fungsi untuk mengirim respons JSON
function respond($success, $message, $extra = [])
{
    echo json_encode(array_merge([
        "success" => $success,
        "message" => $message
    ], $extra));

    exit;
}

// Pastikan pengguna sudah login
if (
    !isset($_SESSION["id_user"]) ||
    ($_SESSION["hak_akses"] ?? "") !== "user"
) {
    http_response_code(401);
    respond(false, "Silakan login terlebih dahulu.");
}

// Pastikan koneksi database tersedia
if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    respond(false, "Koneksi database bermasalah.");
}

// Ambil data dari JavaScript
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    http_response_code(400);
    respond(false, "Data pesanan tidak valid.");
}

// Data pengguna
$id_user = (int) $_SESSION["id_user"];
$nama = trim((string) (
    $input["nama"] ?? $_SESSION["nama"] ?? "Pelanggan COFNA"
));

$telepon = trim((string) ($input["telepon"] ?? ""));
$catatan = trim((string) ($input["catatan"] ?? ""));
$payment = trim((string) ($input["payment"] ?? ""));
$items = $input["items"] ?? [];

// Metode pembayaran yang diperbolehkan
$metodeValid = ["QRIS", "GoPay", "DANA", "OVO"];

if (!in_array($payment, $metodeValid, true)) {
    http_response_code(422);
    respond(false, "Metode pembayaran tidak valid.");
}

if (!is_array($items) || count($items) === 0) {
    http_response_code(422);
    respond(false, "Keranjang pesanan masih kosong.");
}

mysqli_begin_transaction($conn);

try {
    $total = 0;
    $detailItems = [];

    // Ambil harga dan nama menu dari database
    $sqlMenu = "
        SELECT nama_menu, harga
        FROM tb_menu
        WHERE id_menu = ?
        LIMIT 1
    ";

    $stmtMenu = mysqli_prepare($conn, $sqlMenu);

    foreach ($items as $item) {
        $id_menu = (int) ($item["id_menu"] ?? 0);
        $jumlah = (int) (
            $item["jumlah"] ?? $item["quantity"] ?? 1
        );

        if ($id_menu <= 0 || $jumlah <= 0) {
            throw new Exception("Ada item pesanan yang tidak valid.");
        }

        mysqli_stmt_bind_param($stmtMenu, "i", $id_menu);
        mysqli_stmt_execute($stmtMenu);

        $resultMenu = mysqli_stmt_get_result($stmtMenu);
        $menu = mysqli_fetch_assoc($resultMenu);

        if (!$menu) {
            throw new Exception("Menu tidak ditemukan di database.");
        }

        $nama_menu = $menu["nama_menu"];
        $harga = (float) $menu["harga"];
        $subtotal = $harga * $jumlah;

        $total += $subtotal;

        $detailItems[] = [
            "id_menu" => $id_menu,
            "nama_menu" => $nama_menu,
            "harga" => $harga,
            "jumlah" => $jumlah,
            "subtotal" => $subtotal
        ];
    }

    mysqli_stmt_close($stmtMenu);

    // Biaya layanan
    $biaya_layanan = 2000;
    $total += $biaya_layanan;

    if ($total <= 0) {
        throw new Exception("Total pesanan tidak valid.");
    }

    /*
     * Buat nomor pesanan CFN-01, CFN-02, dan seterusnya.
     * Kunci tabel pesanan agar pembuatan nomor lebih aman
     * ketika ada pesanan yang masuk hampir bersamaan.
     */
    mysqli_query(
        $conn,
        "LOCK TABLES tb_pesanan WRITE"
    );

    $resultLast = mysqli_query(
        $conn,
        "SELECT MAX(id_pesanan) AS terakhir FROM tb_pesanan"
    );

    $last = mysqli_fetch_assoc($resultLast);
    $next = ((int) ($last["terakhir"] ?? 0)) + 1;

    $nomor_pesanan = "CFN-" . str_pad(
        (string) $next,
        2,
        "0",
        STR_PAD_LEFT
    );

    mysqli_query($conn, "UNLOCK TABLES");

    // Simpan pesanan utama
    $status = "Sedang Diproses";

    $sqlPesanan = "
        INSERT INTO tb_pesanan
        (
            nomor_pesanan,
            id_user,
            nama_pemesan,
            telepon,
            catatan,
            metode_pembayaran,
            total,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmtPesanan = mysqli_prepare($conn, $sqlPesanan);

    mysqli_stmt_bind_param(
        $stmtPesanan,
        "sissssds",
        $nomor_pesanan,
        $id_user,
        $nama,
        $telepon,
        $catatan,
        $payment,
        $total,
        $status
    );

    mysqli_stmt_execute($stmtPesanan);

    $id_pesanan = mysqli_insert_id($conn);

    mysqli_stmt_close($stmtPesanan);

    // Simpan detail setiap menu yang dipesan
    $sqlDetail = "
        INSERT INTO tb_detail_pesanan
        (
            id_pesanan,
            id_menu,
            nama_menu,
            harga,
            jumlah,
            subtotal
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmtDetail = mysqli_prepare($conn, $sqlDetail);

    foreach ($detailItems as $detail) {
        $id_menu = $detail["id_menu"];
        $nama_menu = $detail["nama_menu"];
        $harga = $detail["harga"];
        $jumlah = $detail["jumlah"];
        $subtotal = $detail["subtotal"];

        mysqli_stmt_bind_param(
            $stmtDetail,
            "iisdid",
            $id_pesanan,
            $id_menu,
            $nama_menu,
            $harga,
            $jumlah,
            $subtotal
        );

        mysqli_stmt_execute($stmtDetail);
    }

    mysqli_stmt_close($stmtDetail);

    // Simpan seluruh transaksi
    mysqli_commit($conn);

    respond(true, "Pesanan berhasil disimpan.", [
        "nomor_pesanan" => $nomor_pesanan,
        "id_pesanan" => $id_pesanan,
        "total" => $total,
        "status" => $status
    ]);

} catch (Throwable $e) {
    try {
        mysqli_query($conn, "UNLOCK TABLES");
    } catch (Throwable $unlockError) {
        // Abaikan jika tabel sudah tidak terkunci
    }

    mysqli_rollback($conn);

    error_log("COFNA proses_pesanan: " . $e->getMessage());

    http_response_code(500);

    respond(
        false,
        "Pesanan gagal disimpan. Periksa struktur tabel dan koneksi database."
    );
}
?>
