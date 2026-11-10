<?php
session_start();

require_once "../config/database.php";

/** @var mysqli $conn */

/* =========================
   CEK LOGIN
========================= */

if (!isset($_SESSION['id_user'])) {
    header("Location: ../html_cofna/login.php");
    exit;
}

if (($_SESSION['hak_akses'] ?? '') !== 'admin') {
    header("Location: ../html_cofna/home_cofna.php");
    exit;
}

$nama = htmlspecialchars($_SESSION['nama'] ?? 'Admin');


/* =========================
   PROSES TAMBAH MENU
========================= */

if (isset($_POST['simpan'])) {

    $nama_menu = trim($_POST['nama_menu'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $harga = $_POST['harga'] ?? 0;

    $gambar = "";


    /* =========================
       UPLOAD GAMBAR
    ========================= */

    if (
        isset($_FILES['gambar']) &&
        $_FILES['gambar']['error'] === 0
    ) {

        $nama_file = $_FILES['gambar']['name'];
        $tmp_file = $_FILES['gambar']['tmp_name'];

        $ekstensi = strtolower(
            pathinfo($nama_file, PATHINFO_EXTENSION)
        );

        $ekstensi_diperbolehkan = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];

        if (!in_array(
            $ekstensi,
            $ekstensi_diperbolehkan
        )) {

            die(
                "Format gambar harus JPG, JPEG, PNG, atau WEBP."
            );
        }


        // Nama file baru
        $nama_baru = time() . "_" . $nama_file;

        $tujuan = "../image_cofna/" . $nama_baru;


        // Pindahkan gambar
        if (move_uploaded_file(
            $tmp_file,
            $tujuan
        )) {

            $gambar = $nama_baru;
        }
    }


    /* =========================
       SIMPAN KE DATABASE
    ========================= */

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO tb_menu
        (nama_menu, deskripsi, kategori, harga, gambar)
        VALUES (?, ?, ?, ?, ?)"
    );


    if (!$stmt) {

        die(
            "Query gagal: " .
            mysqli_error($conn)
        );
    }


    mysqli_stmt_bind_param(
        $stmt,
        "sssis",
        $nama_menu,
        $deskripsi,
        $kategori,
        $harga,
        $gambar
    );


    /* =========================
       JALANKAN QUERY
    ========================= */

    if (mysqli_stmt_execute($stmt)) {

        header("Location: kelola_menu.php");
        exit;

    } else {

        echo "Gagal menambahkan menu: " .
             mysqli_error($conn);
    }

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

    <title>Tambah Menu - COFNA Admin</title>


    <!-- GOOGLE FONT -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

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

        /* =========================
           RESET
        ========================= */

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


        /* =========================
           LAYOUT
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
        }


        .sidebar::before {

            content: "";

            position: absolute;

            width: 180px;
            height: 180px;

            border: 1px solid rgba(
                255,
                255,
                255,
                .12
            );

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
    margin: 0 auto;
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


        /* =========================
           DIVIDER
        ========================= */

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

            min-height: 100vh;

            padding:
                32px 38px 50px;
        }


        /* =========================
           TOPBAR
        ========================= */

        .topbar {

            display: flex;

            justify-content:
                flex-end;

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

            margin-bottom: 25px;
        }


        .page-header h1 {

            font-family:
                'Playfair Display',
                serif;

            font-size: 38px;

            color: #4b291b;

            margin-bottom: 8px;
        }


        .page-header p {

            color: #806c5e;

            font-size: 15px;
        }


        /* =========================
           FORM CARD
        ========================= */

        .form-card {

            max-width: 900px;

            background: white;

            border-radius: 28px;

            padding: 35px;

            box-shadow:
                0 15px 35px
                rgba(91,55,35,.08);

            position: relative;

            overflow: hidden;
        }


        .form-card::before {

            content: "☕";

            position: absolute;

            right: -25px;

            top: -35px;

            font-size: 150px;

            opacity: .035;

            transform: rotate(15deg);
        }


        /* =========================
           FORM
        ========================= */

        .form-group {

            margin-bottom: 22px;

            position: relative;

            z-index: 2;
        }


        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: 700;

            color: #4b2e20;
        }


        input,
        select,
        textarea {

            width: 100%;

            padding: 14px 16px;

            border:
                1px solid #dfd1c3;

            border-radius: 14px;

            font-family: inherit;

            font-size: 14px;

            background: #fffdfb;

            color: #4b2e20;

            transition: .2s;
        }


        input:focus,
        select:focus,
        textarea:focus {

            outline: none;

            border-color: #a96f4c;

            box-shadow:
                0 0 0 4px
                rgba(169,111,76,.10);
        }


        textarea {

            min-height: 130px;

            resize: vertical;
        }


        input[type="file"] {

            padding: 12px;

            background: #fffaf5;

            cursor: pointer;
        }


        /* =========================
           HINT
        ========================= */

        .hint {

            margin-top: 7px;

            font-size: 12px;

            color: #9a8677;
        }


        /* =========================
           BUTTON
        ========================= */

        .form-buttons {

            display: flex;

            gap: 12px;

            margin-top: 30px;

            position: relative;

            z-index: 2;
        }


        .btn-save,
        .btn-cancel {

            padding:
                13px 22px;

            border-radius: 14px;

            font-size: 14px;

            font-weight: 600;

            transition: .25s;
        }


        .btn-save {

            border: none;

            background: #673e28;

            color: white;

            cursor: pointer;

            box-shadow:
                0 8px 18px
                rgba(103,62,40,.18);
        }


        .btn-save:hover {

            background: #7c4d31;

            transform:
                translateY(-2px);
        }


        .btn-cancel {

            background: #f0e3d5;

            color: #633b27;
        }


        .btn-cancel:hover {

            background: #e3d0bd;

            transform:
                translateY(-2px);
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

                width:
                    calc(100% - 230px);

                padding: 25px;
            }

        }


        @media (max-width: 650px) {

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

                padding: 22px;
            }


            .form-card {

                padding: 25px;
            }


            .page-header h1 {

                font-size: 30px;
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
            <span class="side-icon">🏠</span>
            <span>Beranda</span>
        </a>

        <a href="kelola_menu.php">
            <span class="side-icon">☕</span>
            <span>Kelola Menu</span>
        </a>

        <a href="tambah_menu.php" class="active">
            <span class="side-icon">＋</span>
            <span>Tambah Menu</span>
        </a>

        <a href="kelola_pesanan.php">
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
                        <?= $nama ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </div>



        <!-- PAGE HEADER -->

        <div class="page-header">

            <h1>
                Tambah Menu ☕
            </h1>

            <p>
                Tambahkan minuman baru ke dalam menu COFNA.
            </p>

        </div>



        <!-- FORM -->

        <div class="form-card">


            <form
                action=""
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- NAMA -->

                <div class="form-group">

                    <label for="nama_menu">
                        Nama Menu *
                    </label>

                    <input
                        type="text"
                        id="nama_menu"
                        name="nama_menu"
                        placeholder="Contoh: Caramel Latte"
                        required
                    >

                </div>



                <!-- DESKRIPSI -->

                <div class="form-group">

                    <label for="deskripsi">
                        Deskripsi *
                    </label>

                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        placeholder="Contoh: Kopi dengan perpaduan susu dan caramel yang lembut..."
                        required
                    ></textarea>

                </div>



                <!-- KATEGORI -->

                <div class="form-group">

                    <label for="kategori">
                        Kategori *
                    </label>

                    <select
                        id="kategori"
                        name="kategori"
                        required
                    >

                        <option value="">
                            -- Pilih Kategori --
                        </option>

                        <option value="Coffee">
                            Coffee
                        </option>

                        <option value="Non-Coffee">
                            Non-Coffee
                        </option>

                        <option value="Favorite">
                            Favorite
                        </option>

                    </select>

                </div>



                <!-- HARGA -->

                <div class="form-group">

                    <label for="harga">
                        Harga *
                    </label>

                    <input
                        type="number"
                        id="harga"
                        name="harga"
                        placeholder="Contoh: 18000"
                        min="0"
                        required
                    >

                    <div class="hint">
                        Masukkan harga dalam Rupiah tanpa titik.
                        Contoh: 18000
                    </div>

                </div>



                <!-- GAMBAR -->

                <div class="form-group">

                    <label for="gambar">
                        Gambar Menu
                    </label>

                    <input
                        type="file"
                        id="gambar"
                        name="gambar"
                        accept=".jpg,.jpeg,.png,.webp"
                    >

                    <div class="hint">
                        Format yang diperbolehkan:
                        JPG, JPEG, PNG, WEBP
                    </div>

                </div>



                <!-- BUTTON -->

                <div class="form-buttons">


                    <a
                        href="kelola_menu.php"
                        class="btn-cancel"
                    >
                        ← Batal
                    </a>


                    <button
                        type="submit"
                        name="simpan"
                        class="btn-save"
                    >
                        💾 Simpan Menu
                    </button>


                </div>


            </form>


        </div>


    </main>


</div>


</body>

</html>