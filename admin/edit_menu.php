<?php

session_start();

require_once "../config/database.php";

/** @var mysqli $conn */

// =========================
// CEK LOGIN
// =========================

if (!isset($_SESSION['id_user'])) {
    header("Location: ../html_cofna/login.php");
    exit;
}


// =========================
// CEK ADMIN
// =========================

if (($_SESSION['hak_akses'] ?? '') !== 'admin') {
    header("Location: ../html_cofna/home_cofna.php");
    exit;
}


// =========================
// CEK ID MENU
// =========================

$id_menu = $_GET['id'] ?? 0;

$id_menu = intval($id_menu);

if ($id_menu <= 0) {
    header("Location: kelola_menu.php");
    exit;
}


// =========================
// AMBIL DATA MENU
// =========================

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM tb_menu WHERE id_menu = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id_menu
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$menu = mysqli_fetch_assoc($result);


// Jika menu tidak ditemukan

if (!$menu) {
    die("Menu tidak ditemukan.");
}


// =========================
// PROSES UPDATE
// =========================

if (isset($_POST['simpan'])) {

    $nama_menu = trim(
        $_POST['nama_menu'] ?? ''
    );

    $deskripsi = trim(
        $_POST['deskripsi'] ?? ''
    );

    $kategori = trim(
        $_POST['kategori'] ?? ''
    );

    $harga = $_POST['harga'] ?? 0;


    // Gunakan gambar lama terlebih dahulu

    $gambar = $menu['gambar'];


    // =========================
    // CEK GAMBAR BARU
    // =========================

    if (
        isset($_FILES['gambar']) &&
        $_FILES['gambar']['error'] === 0
    ) {

        $nama_file = $_FILES['gambar']['name'];

        $tmp_file = $_FILES['gambar']['tmp_name'];


        $ekstensi = strtolower(
            pathinfo(
                $nama_file,
                PATHINFO_EXTENSION
            )
        );


        $ekstensi_diperbolehkan = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];


        if (
            !in_array(
                $ekstensi,
                $ekstensi_diperbolehkan
            )
        ) {

            die(
                "Format gambar harus JPG, JPEG, PNG, atau WEBP."
            );

        }


        // Nama file baru

        $nama_baru = time() . "_" . $nama_file;


        $tujuan =
            "../image_cofna/" . $nama_baru;


        // Upload gambar baru

        if (
            move_uploaded_file(
                $tmp_file,
                $tujuan
            )
        ) {

            $gambar = $nama_baru;

        }

    }


    // =========================
    // UPDATE DATABASE
    // =========================

    $update = mysqli_prepare(
        $conn,
        "UPDATE tb_menu
         SET
            nama_menu = ?,
            deskripsi = ?,
            kategori = ?,
            harga = ?,
            gambar = ?
         WHERE id_menu = ?"
    );


    if (!$update) {

        die(
            "Query update gagal: " .
            mysqli_error($conn)
        );

    }


    mysqli_stmt_bind_param(
        $update,
        "sssisi",
        $nama_menu,
        $deskripsi,
        $kategori,
        $harga,
        $gambar,
        $id_menu
    );


    // =========================
    // JALANKAN UPDATE
    // =========================

    if (mysqli_stmt_execute($update)) {

        header(
            "Location: kelola_menu.php"
        );

        exit;

    } else {

        echo "Gagal mengubah menu: " .
             mysqli_error($conn);

    }

}


// Nama admin

$nama_admin = htmlspecialchars(
    $_SESSION['nama'] ?? 'Admin'
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Menu - COFNA</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;

            font-family:
                "Segoe UI",
                Arial,
                sans-serif;

            background: #f7efe4;

            color: #4b2e20;
        }


        /* =========================
           LAYOUT
           ========================= */

        .admin-layout {
            min-height: 100vh;

            display: flex;
        }


        /* =========================
           SIDEBAR
           ========================= */

        .sidebar {
            width: 245px;

            min-height: 100vh;

            background:
                linear-gradient(
                    180deg,
                    #4b2e20,
                    #5f3c29
                );

            color: white;

            padding: 28px 18px;

            position: fixed;

            left: 0;

            top: 0;

            bottom: 0;
        }


        .logo {
            text-align: center;

            margin-bottom: 35px;
        }


        .logo-icon {
            font-size: 34px;
        }


        .logo h2 {
            margin:
                5px 0 3px;

            font-size: 25px;

            letter-spacing: 1px;
        }


        .logo p {
            margin: 0;

            font-size: 12px;

            opacity: 0.7;
        }


        .menu-title {
            font-size: 11px;

            text-transform: uppercase;

            opacity: 0.55;

            margin:
                20px 12px 8px;
        }


        .sidebar a {
            display: flex;

            align-items: center;

            gap: 12px;

            color: white;

            text-decoration: none;

            padding: 13px 14px;

            margin: 5px 0;

            border-radius: 12px;

            transition: 0.2s;

            font-size: 14px;
        }


        .sidebar a:hover,
        .sidebar a.active {
            background: #9a6b4d;
        }


        .sidebar-icon {
            width: 24px;

            text-align: center;

            font-size: 18px;
        }


        .sidebar-line {
            height: 1px;

            background:
                rgba(
                    255,
                    255,
                    255,
                    0.2
                );

            margin:
                20px 10px;
        }


        /* =========================
           MAIN
           ========================= */

        .main {
            margin-left: 245px;

            width:
                calc(100% - 245px);

            min-height: 100vh;

            padding:
                28px 40px;
        }


        /* =========================
           TOPBAR
           ========================= */

        .topbar {
            display: flex;

            justify-content: flex-end;

            align-items: center;

            margin-bottom: 25px;
        }


        .admin-user {
            display: flex;

            align-items: center;

            gap: 10px;

            background: white;

            padding:
                9px 14px;

            border-radius: 30px;

            box-shadow:
                0 4px 15px
                rgba(
                    75,
                    46,
                    32,
                    0.08
                );

            font-size: 13px;
        }


        .user-icon {
            width: 32px;

            height: 32px;

            background: #ead7c4;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;
        }


        /* =========================
           TITLE
           ========================= */

        .page-title {
            margin-bottom: 22px;
        }


        .page-title h1 {
            margin:
                0 0 6px;

            font-size: 30px;

            color: #3e2418;
        }


        .page-title p {
            margin: 0;

            color: #806b5b;

            font-size: 14px;
        }


        /* =========================
           FORM CARD
           ========================= */

        .form-card {
            max-width: 850px;

            background: white;

            border-radius: 22px;

            padding: 35px;

            box-shadow:
                0 8px 25px
                rgba(
                    75,
                    46,
                    32,
                    0.07
                );
        }


        .form-group {
            margin-bottom: 20px;
        }


        .form-group label {
            display: block;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 600;
        }


        input,
        select,
        textarea {
            width: 100%;

            padding:
                12px 14px;

            border:
                1px solid #ded2c5;

            border-radius: 10px;

            font-family: inherit;

            font-size: 14px;

            background: #fffdfb;

            color: #4b2e20;
        }


        textarea {
            min-height: 110px;

            resize: vertical;
        }


        input:focus,
        select:focus,
        textarea:focus {

            outline: none;

            border-color: #8b6043;

            box-shadow:
                0 0 0 3px
                rgba(
                    139,
                    96,
                    67,
                    0.1
                );
        }


        input[type="file"] {
            padding: 10px;

            background: #fffaf5;

            cursor: pointer;
        }


        /* =========================
           GAMBAR LAMA
           ========================= */

        .gambar-sekarang {
            margin-top: 10px;

            padding: 12px;

            background: #fff8f1;

            border-radius: 12px;

            display: inline-block;
        }


        .gambar-sekarang img {
            width: 100px;

            height: 100px;

            object-fit: cover;

            border-radius: 12px;

            display: block;

            margin-bottom: 7px;
        }


        .gambar-sekarang span {
            font-size: 11px;

            color: #806b5b;
        }


        /* =========================
           BUTTON
           ========================= */

        .form-buttons {
            display: flex;

            gap: 10px;

            margin-top: 28px;
        }


        .btn-save,
        .btn-cancel {
            padding:
                13px 22px;

            border-radius: 10px;

            font-size: 14px;

            text-decoration: none;

            transition: 0.2s;
        }


        .btn-save {
            border: none;

            background: #6f4932;

            color: white;

            cursor: pointer;
        }


        .btn-save:hover {
            background: #573622;

            transform:
                translateY(-2px);
        }


        .btn-cancel {
            background: #eee5dc;

            color: #4b2e20;
        }


        .btn-cancel:hover {
            background: #e2d5c8;

            transform:
                translateY(-2px);
        }


        /* =========================
           RESPONSIVE
           ========================= */

        @media (max-width: 800px) {

            .sidebar {
                width: 190px;
            }


            .main {
                margin-left: 190px;

                width:
                    calc(100% - 190px);

                padding: 20px;
            }

        }


        @media (max-width: 600px) {

            .admin-layout {
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
            }

        }

    </style>

</head>


<body>


<div class="admin-layout">


    <!-- =========================
         SIDEBAR
         ========================= -->

    <aside class="sidebar">


        <div class="logo">

            <div class="logo-icon">
                ☕
            </div>

            <h2>
                COFNA
            </h2>

            <p>
                Admin Panel
            </p>

        </div>


        <div class="menu-title">
            Menu
        </div>


        <a href="index.php">

            <span class="sidebar-icon">
                🏠
            </span>

            Beranda

        </a>


        <a
            href="kelola_menu.php"
            class="active"
        >

            <span class="sidebar-icon">
                ☕
            </span>

            Kelola Menu

        </a>


        <a href="tambah_menu.php">

            <span class="sidebar-icon">
                ➕
            </span>

            Tambah Menu

        </a>


        <div class="sidebar-line"></div>


        <a href="../html_cofna/home_cofna.php">

            <span class="sidebar-icon">
                🏡
            </span>

            Ke Home

        </a>


        <a href="../html_cofna/logout.php">

            <span class="sidebar-icon">
                🚪
            </span>

            Logout

        </a>


    </aside>



    <!-- =========================
         MAIN
         ========================= -->

    <main class="main">


        <!-- TOPBAR -->

        <div class="topbar">

            <div class="admin-user">

                <div class="user-icon">
                    👤
                </div>

                <span>
                    <?= $nama_admin ?>
                </span>

            </div>

        </div>



        <!-- TITLE -->

        <div class="page-title">

            <h1>
                ✏️ Edit Menu
            </h1>

            <p>
                Ubah informasi menu COFNA.
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
                        value="<?= htmlspecialchars($menu['nama_menu']) ?>"
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
                        required
                    ><?= htmlspecialchars($menu['deskripsi']) ?></textarea>

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


                        <option
                            value="Coffee"
                            <?= $menu['kategori'] === 'Coffee' ? 'selected' : '' ?>
                        >
                            Coffee
                        </option>


                        <option
                            value="Non-Coffee"
                            <?= $menu['kategori'] === 'Non-Coffee' ? 'selected' : '' ?>
                        >
                            Non-Coffee
                        </option>


                        <option
                            value="Favorite"
                            <?= $menu['kategori'] === 'Favorite' ? 'selected' : '' ?>
                        >
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
                        value="<?= htmlspecialchars($menu['harga']) ?>"
                        min="0"
                        required
                    >

                </div>



                <!-- GAMBAR -->

                <div class="form-group">

                    <label for="gambar">
                        Ganti Gambar
                    </label>


                    <input
                        type="file"
                        id="gambar"
                        name="gambar"
                        accept=".jpg,.jpeg,.png,.webp"
                    >


                    <?php if (!empty($menu['gambar'])): ?>

                        <div class="gambar-sekarang">

                            <img
                                src="../image_cofna/<?= htmlspecialchars($menu['gambar']) ?>"
                                alt="Gambar menu"
                            >

                            <span>
                                Gambar saat ini
                            </span>

                        </div>

                    <?php endif; ?>

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
                        💾 Simpan Perubahan
                    </button>


                </div>


            </form>


        </div>


    </main>


</div>


</body>

</html>