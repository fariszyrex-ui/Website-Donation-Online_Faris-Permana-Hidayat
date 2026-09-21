<?php
session_start();

require_once "config/database.php";
require_once "functions/fungsi_umum.php";

/* =========================
   AUTO CLOSE CAMPAIGN EXPIRED
========================= */
updateCampaignExpired($pdo);

/* =========================
   AMBIL CAMPAIGN AKTIF
========================= */
$queryCampaign = $pdo->query("
    SELECT *
    FROM campaign
    WHERE status = 'aktif'
    ORDER BY id_campaign DESC
    LIMIT 6
");

$campaigns = $queryCampaign->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   AMBIL BERITA TERBARU
========================= */
$queryBerita = $pdo->query("
    SELECT berita.*, campaign.judul AS judul_campaign
    FROM berita
    INNER JOIN campaign
        ON berita.id_campaign = campaign.id_campaign
    ORDER BY berita.id_berita DESC
    LIMIT 3
");

$berita = $queryBerita->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Donasi Online</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@400;500&display=swap"
    rel="stylesheet"
>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>


<!-- ======================================================
     NAVBAR
======================================================= -->

<header class="navbar" id="navbar">

    <div class="nav-container">

        <a href="index.php" class="logo">
            Donasi Online
        </a>


        <nav class="nav-menu">

            <a href="index.php">
                Home
            </a>

            <a href="pages/campaign.php">
                Campaign
            </a>

            <a href="pages/berita.php">
                Berita
            </a>

            <a href="pages/tentang.php">
                Tentang
            </a>

            <a href="pages/kontak.php">
                Kontak
            </a>

        </nav>


        <?php if (isset($_SESSION["login"]) && $_SESSION["login"] === true): ?>

            <div class="nav-user">

                <span class="user-name">
                    <?= htmlspecialchars($_SESSION["nama"]) ?>
                </span>

                <?php if ($_SESSION["role"] === "donatur"): ?>

                    <a href="pages/riwayat.php">
                        Riwayat
                    </a>

                <?php endif; ?>

                <a href="auth/logout.php">
                    Logout
                </a>

            </div>

        <?php else: ?>

            <div class="nav-user">

                <a href="auth/auth.php">
                    Login
                </a>

            </div>

        <?php endif; ?>


        <button class="menu-toggle" id="menuToggle">

            <span></span>
            <span></span>
            <span></span>

        </button>

    </div>

</header>


<!-- ======================================================
     MOBILE MENU
======================================================= -->

<div class="mobile-menu" id="mobileMenu">

    <a href="index.php">
        Home
    </a>

    <a href="pages/campaign.php">
        Campaign
    </a>

    <a href="pages/berita.php">
        Berita
    </a>

    <a href="pages/tentang.php">
        Tentang
    </a>

    <a href="pages/kontak.php">
        Kontak
    </a>


    <?php if (isset($_SESSION["login"]) && $_SESSION["login"] === true): ?>

        <?php if ($_SESSION["role"] === "donatur"): ?>

            <a href="pages/riwayat.php">
                Riwayat
            </a>

        <?php endif; ?>

        <a href="auth/logout.php">
            Logout
        </a>

    <?php else: ?>

        <a href="auth/auth.php">
            Login
        </a>

    <?php endif; ?>

</div>


<!-- ======================================================
     HERO
======================================================= -->

<section class="hero">

    <video
        class="hero-video"
        autoplay
        muted
        loop
        playsinline
        preload="auto"
        poster="assets/images/olek-buzunov-uCHUP_skz2M-unsplash.jpg"
    >
        <source
            src="assets/images/snaptik_7555938716062190849_v2.mp4"
            type="video/mp4"
        >
    </video>

    <div class="hero-overlay"></div>

    <div class="hero-content reveal">

        <div class="hero-label">
            Bersama Membawa Perubahan
        </div>

        <h1>
            Satu Donasi.<br>
            Satu Harapan.
        </h1>

        <p class="hero-description">
            Setiap bantuan yang diberikan menjadi bagian
            dari perubahan nyata bagi mereka yang membutuhkan.
        </p>

        <a
            href="pages/campaign.php"
            class="hero-button"
        >
            Lihat Campaign
        </a>

    </div>

    
</section>


<!-- ======================================================
     INTRO
======================================================= -->

<section class="section">

    <div class="intro reveal">

        <div>

            <div class="section-label">
                Tentang Platform
            </div>

            <h2>
                Perubahan<br>
                Dimulai Dari Kita.
            </h2>

        </div>


        <div class="intro-text">

            Donasi Online merupakan platform yang
            mempertemukan orang-orang yang ingin membantu
            dengan berbagai campaign sosial yang membutuhkan
            dukungan.

            <br><br>

            Bersama, kita dapat menciptakan dampak yang
            lebih berarti.

        </div>

    </div>

</section>


<!-- ======================================================
     IMAGE BAND
======================================================= -->

<section class="image-band">

    <img
        src="assets/images/olek-buzunov-uCHUP_skz2M-unsplash.jpg"
        alt="Kegiatan sosial"
    >

</section>


<!-- ======================================================
     CAMPAIGN
======================================================= -->

<section class="section">

    <div class="section-header reveal">

        <div>

            <div class="section-label">
                Campaign
            </div>

            <h2 class="section-title">
                Campaign Aktif
            </h2>

        </div>

        <a
            href="pages/campaign.php"
            class="section-link"
        >
            Lihat Semua →
        </a>

    </div>


    <?php if (count($campaigns) > 0): ?>

        <div class="campaign-grid">

            <?php foreach ($campaigns as $campaign): ?>

                <?php

                $target = (float) $campaign["target_donasi"];
                $terkumpul = (float) $campaign["dana_terkumpul"];

                $persentase = $target > 0
                    ? ($terkumpul / $target) * 100
                    : 0;

                $persentase = min($persentase, 100);

                // TAMBAHAN: hitung sisa hari untuk gaya "urgent"
                // pada badge deadline (<=3 hari dianggap mendesak)
                $sisaDetik = strtotime($campaign["tanggal_batas"]) - time();
                $sisaHariNum = $sisaDetik / 86400;
                $deadlineClass = $sisaHariNum <= 3
                    ? "deadline urgent"
                    : "deadline";

                ?>

                <article class="campaign-card reveal">

                    <div class="campaign-image">

                        <div class="campaign-badge">
                            <?= number_format($persentase, 0) ?>% Terkumpul
                        </div>

                        <?php if (!empty($campaign["gambar"])): ?>

                            <img
                                src="uploads/campaign/<?= htmlspecialchars($campaign["gambar"]) ?>"
                                alt="<?= htmlspecialchars($campaign["judul"]) ?>"
                            >

                        <?php else: ?>

                            <img
                                src="assets/images/olek-buzunov-uCHUP_skz2M-unsplash.jpg"
                                alt="Campaign"
                            >

                        <?php endif; ?>

                    </div>


                    <div class="campaign-body">

                        <h3>
                            <?= htmlspecialchars($campaign["judul"]) ?>
                        </h3>


                        <p class="campaign-description">

                            <?= htmlspecialchars(
                                mb_strimwidth(
                                    strip_tags($campaign["deskripsi"]),
                                    0,
                                    140,
                                    "..."
                                )
                            ) ?>

                        </p>


                        <div class="campaign-progress">

                            <div class="progress-label">

                                <span>
                                    Terkumpul
                                </span>

                                <span>
                                    <?= number_format(
                                        $persentase,
                                        0
                                    ) ?>%
                                </span>

                            </div>


                            <div class="progress-bar">

                                <div
                                    class="progress-fill"
                                    style="width: <?= $persentase ?>%"
                                ></div>

                            </div>


                            <div class="progress-label"
                                 style="margin-top:10px;">

                                <span>
                                    <?= formatRupiah($terkumpul) ?>
                                </span>

                                <span>
                                    <?= formatRupiah($target) ?>
                                </span>

                            </div>

                        </div>


                        <div
                            class="<?= $deadlineClass ?>"
                            data-deadline="<?= date(
                                'Y-m-d H:i:s',
                                strtotime($campaign['tanggal_batas'])
                            ) ?>"
                        >
                            ⏳ Memuat waktu...
                        </div>


                        <a
                            href="pages/detail_campaign.php?id=<?= $campaign["id_campaign"] ?>"
                            class="card-button"
                        >
                            Lihat Campaign
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="reveal"
             style="
                padding:80px 20px;
                border-top:1px solid #222;
                border-bottom:1px solid #222;
                color:#777;
                text-align:center;
             ">

            Belum ada campaign aktif.

        </div>

    <?php endif; ?>

</section>


<!-- ======================================================
     NEWS
======================================================= -->

<section class="section">

    <div class="section-header reveal">

        <div>

            <div class="section-label">
                Newsroom
            </div>

            <h2 class="section-title">
                Berita Terbaru
            </h2>

        </div>

        <a
            href="pages/berita.php"
            class="section-link"
        >
            Semua Berita →
        </a>

    </div>


    <?php if (count($berita) > 0): ?>

        <div class="news-grid">

            <?php foreach ($berita as $item): ?>

                <article class="news-card reveal">

                    <div class="news-image">

                        <?php if (!empty($item["gambar"])): ?>

                            <img
                                src="uploads/berita/<?= htmlspecialchars($item["gambar"]) ?>"
                                alt="<?= htmlspecialchars($item["judul"]) ?>"
                            >

                        <?php else: ?>

                            <img
                                src="assets/images/olek-buzunov-uCHUP_skz2M-unsplash.jpg"
                                alt="Berita"
                            >

                        <?php endif; ?>

                        <div class="news-date">

                            <?= formatTanggal($item["tanggal"]) ?>

                        </div>

                    </div>


                    <h3>

                        <?= htmlspecialchars($item["judul"]) ?>

                    </h3>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="reveal"
             style="
                padding:60px 20px;
                border-top:1px solid #222;
                color:#777;
                text-align:center;
             ">

            Belum ada berita.

        </div>

    <?php endif; ?>

</section>


<!-- ======================================================
     CTA
======================================================= -->

<section class="cta">

    <img
        src="assets/images/olek-buzunov-uCHUP_skz2M-unsplash.jpg"
        alt="Bersama membantu"
    >


    <div class="cta-content reveal">

        <h2>
            Jadilah Bagian Dari Perubahan.
        </h2>

        <p>
            Tidak harus besar untuk berarti.
            Satu bantuan dapat menjadi awal dari
            harapan seseorang.
        </p>

        <a
            href="pages/campaign.php"
            class="cta-button"
        >
            Mulai Berdonasi
        </a>

    </div>

</section>


<!-- ======================================================
     FOOTER
======================================================= -->

<footer>

    <div class="footer-container">

        <div class="footer-top">


            <div>

                <div class="footer-brand">
                    Donasi Online
                </div>

            </div>


            <div class="footer-column">

                <h4>
                    Navigasi
                </h4>

                <a href="index.php">
                    Home
                </a>

                <a href="pages/campaign.php">
                    Campaign
                </a>

                <a href="pages/berita.php">
                    Berita
                </a>

            </div>


            <div class="footer-column">

                <h4>
                    Informasi
                </h4>

                <a href="pages/tentang.php">
                    Tentang
                </a>

                <a href="pages/kontak.php">
                    Kontak
                </a>

            </div>


            <div class="footer-column">

                <h4>
                    Akun
                </h4>

                <?php if (
                    isset($_SESSION["login"])
                    && $_SESSION["login"] === true
                ): ?>

                    <?php if ($_SESSION["role"] === "donatur"): ?>

                        <a href="pages/riwayat.php">
                            Riwayat Donasi
                        </a>

                    <?php endif; ?>

                    <a href="auth/logout.php">
                        Logout
                    </a>

                <?php else: ?>

                    <a href="auth/auth.php">
                        Login
                    </a>

                <?php endif; ?>

            </div>


        </div>


        <div class="footer-bottom">

            <span>
                © <?= date("Y") ?> Donasi Online
            </span>

            <span>
                Together For A Better Future
            </span>

        </div>

    </div>

</footer>


<script src="assets/js/script.js"></script>

</body>
</html>