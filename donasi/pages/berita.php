<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../config/database.php";

$query = $pdo->query("
    SELECT
        berita.id_berita,
        berita.judul,
        berita.isi,
        berita.gambar,
        berita.tanggal,
        campaign.judul AS judul_campaign
    FROM berita
    INNER JOIN campaign
        ON berita.id_campaign = campaign.id_campaign
    ORDER BY berita.id_berita DESC
");

$berita = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Berita — Donasi Online</title>

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
     BERITA
===================================================== -->

<section class="section">

    <div class="section-header">

        <div>

            <p class="section-label">
                DONASI ONLINE
            </p>

            <h1 class="section-title">
                Berita
            </h1>

        </div>

    </div>

    <?php if (count($berita) > 0): ?>

        <div class="news-grid">

            <?php foreach ($berita as $b): ?>

                <article class="news-card">

                    <?php if (!empty($b["gambar"])): ?>

                        <div class="news-image">
                            <img
                                src="../uploads/berita/<?= htmlspecialchars($b["gambar"]); ?>"
                                alt="<?= htmlspecialchars($b["judul"]); ?>"
                            >
                        </div>

                    <?php endif; ?>

                    <div class="news-date">
                        <?= htmlspecialchars($b["tanggal"]); ?>
                    </div>

                    <h3>
                        <?= htmlspecialchars($b["judul"]); ?>
                    </h3>

                    <p class="intro-text" style="font-size: 15px; margin-top: 10px;">
                        Campaign:
                        <strong><?= htmlspecialchars($b["judul_campaign"]); ?></strong>
                    </p>

                    <p class="campaign-description">
                        <?= htmlspecialchars(
                            mb_substr($b["isi"], 0, 150)
                        ); ?>...
                    </p>

                    <a
                        class="card-button"
                        href="detail_berita.php?id=<?= $b["id_berita"]; ?>"
                        style="margin-top: 20px;"
                    >
                        Baca Selengkapnya
                        <span>→</span>
                    </a>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <p class="section-label">
            BERITA
        </p>

        <h2 class="section-title">
            Belum ada berita.
        </h2>

    <?php endif; ?>

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