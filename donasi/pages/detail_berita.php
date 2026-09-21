<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../config/database.php";

if (!isset($_GET["id"])) {
    die("Berita tidak ditemukan.");
}

$id_berita = $_GET["id"];

$query = $pdo->prepare("
    SELECT
        berita.id_berita,
        berita.judul,
        berita.isi,
        berita.gambar,
        berita.tanggal,
        campaign.id_campaign,
        campaign.judul AS judul_campaign
    FROM berita
    INNER JOIN campaign
        ON berita.id_campaign = campaign.id_campaign
    WHERE berita.id_berita = ?
");

$query->execute([$id_berita]);

$berita = $query->fetch(PDO::FETCH_ASSOC);

if (!$berita) {
    die("Berita tidak ditemukan.");
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
        <?= htmlspecialchars($berita["judul"]); ?> — Donasi Online
    </title>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@400;500&display=swap"
        rel="stylesheet"
    >

    <!-- Main CSS -->
    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar">

    <div class="nav-container">

        <a
            href="../index.php"
            class="logo"
        >
            DONASI ONLINE
        </a>

        <nav class="nav-menu">

            <a href="../index.php">
                Home
            </a>

            <a href="campaign.php">
                Campaign
            </a>

            <a
                href="berita.php"
                class="active"
            >
                Berita
            </a>

            <a href="tentang.php">
                Tentang
            </a>

            <a href="kontak.php">
                Kontak
            </a>

        </nav>

        <div class="nav-user">

            <?php if (
                isset($_SESSION["login"]) &&
                $_SESSION["login"] === true
            ): ?>

                <span class="user-name">
                    <?= htmlspecialchars($_SESSION["nama"]) ?>
                </span>

                <a href="riwayat.php">
                    Riwayat
                </a>

                <a href="../auth/logout.php">
                    Logout
                </a>

            <?php else: ?>

                <a href="../auth/auth.php">
                    Login
                </a>

            <?php endif; ?>

        </div>

        <button class="menu-toggle" type="button" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>

</header>


<!-- =====================================================
     MOBILE MENU
===================================================== -->

<div class="mobile-menu">

    <a href="../index.php">
        Home
    </a>

    <a href="campaign.php">
        Campaign
    </a>

    <a href="berita.php">
        Berita
    </a>

    <a href="tentang.php">
        Tentang
    </a>

    <a href="kontak.php">
        Kontak
    </a>

    <?php if (
        isset($_SESSION["login"]) &&
        $_SESSION["login"] === true
    ): ?>

        <a href="riwayat.php">
            Riwayat
        </a>

        <a href="../auth/logout.php">
            Logout
        </a>

    <?php else: ?>

        <a href="../auth/auth.php">
            Login
        </a>

    <?php endif; ?>

</div>


<!-- =====================================================
     DETAIL BERITA
===================================================== -->

<section class="section">

    <a
        href="berita.php"
        class="section-link"
    >
        &larr; Kembali ke Berita
    </a>

    <div class="section-header" style="margin-top: 25px;">

        <div>

            <p class="section-label">
                DETAIL BERITA
            </p>

            <h1 class="section-title">
                <?= htmlspecialchars($berita["judul"]); ?>
            </h1>

        </div>

    </div>

    <?php if (!empty($berita["gambar"])): ?>

        <div class="image-band">
            <img
                src="../uploads/berita/<?= htmlspecialchars($berita["gambar"]); ?>"
                alt="<?= htmlspecialchars($berita["judul"]); ?>"
            >
        </div>

    <?php endif; ?>

    <div class="news-date" style="margin-top: 30px;">
        <?= htmlspecialchars($berita["tanggal"]); ?>
    </div>

    <p class="intro-text" style="font-size: 15px; margin-top: 10px;">
        Berita dari Campaign:
        <strong><?= htmlspecialchars($berita["judul_campaign"]); ?></strong>
    </p>

    <p class="intro-text" style="margin-top: 25px;">
        <?= nl2br(htmlspecialchars($berita["isi"])); ?>
    </p>

    <a
        href="../index.php"
        class="section-link"
        style="display: inline-block; margin-top: 40px;"
    >
        &larr; Kembali ke Daftar Berita
    </a>

</section>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer>

    <div class="footer-container">

        <div class="footer-top">

            <div>

                <div class="footer-brand">
                    DONASI ONLINE
                </div>

                <p class="intro-text">
                    Platform donasi untuk menghubungkan
                    kebaikan dengan mereka yang membutuhkan.
                </p>

            </div>


            <div class="footer-column">

                <h4>
                    Navigasi
                </h4>

                <a href="../index.php">
                    Home
                </a>

                <a href="campaign.php">
                    Campaign
                </a>

                <a href="berita.php">
                    Berita
                </a>

            </div>


            <div class="footer-column">

                <h4>
                    Informasi
                </h4>

                <a href="tentang.php">
                    Tentang
                </a>

                <a href="kontak.php">
                    Kontak
                </a>

            </div>


            <div class="footer-column">

                <h4>
                    Akun
                </h4>

                <?php if (
                    isset($_SESSION["login"]) &&
                    $_SESSION["login"] === true
                ): ?>

                    <a href="riwayat.php">
                        Riwayat Donasi
                    </a>

                    <a href="../auth/logout.php">
                        Logout
                    </a>

                <?php else: ?>

                    <a href="../auth/auth.php">
                        Login
                    </a>

                <?php endif; ?>

            </div>

        </div>


        <div class="footer-bottom">

            <span>
                © <?= date("Y") ?> Donasi Online.
                All rights reserved.
            </span>

        </div>

    </div>

</footer>

</body>

</html>