<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tentang Kami — Donasi Online</title>

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

            <a href="berita.php">
                Berita
            </a>

            <a
                href="tentang.php"
                class="active"
            >
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
     TENTANG KAMI
===================================================== -->

<section class="section">

    <div class="intro">

        <div>

            <p class="section-label">
                TENTANG KAMI
            </p>

            <h1 class="section-title" style="margin-top: 15px;">
                Menghubungkan<br>
                kebaikan.
            </h1>

        </div>

        <p class="intro-text">
            Donasi Online adalah platform yang mempertemukan
            kebaikan para donatur dengan campaign-campaign yang
            membutuhkan dukungan. Kami percaya bahwa setiap donasi,
            sekecil apa pun, dapat membawa perubahan nyata bagi
            mereka yang membutuhkan.
        </p>

    </div>

</section>


<section class="section" style="padding-top: 0;">

    <div class="section-header">

        <div>

            <p class="section-label">
                MISI KAMI
            </p>

            <h2 class="section-title">
                Kenapa Donasi Online?
            </h2>

        </div>

    </div>

    <div class="campaign-grid">

        <article class="campaign-card">

            <div class="campaign-body">

                <h3>Transparan</h3>

                <p class="campaign-description">
                    Setiap campaign menampilkan target dan dana
                    yang sudah terkumpul secara terbuka, sehingga
                    donatur dapat memantau perkembangannya.
                </p>

            </div>

        </article>

        <article class="campaign-card">

            <div class="campaign-body">

                <h3>Mudah Diakses</h3>

                <p class="campaign-description">
                    Proses donasi dirancang sederhana, mulai dari
                    memilih campaign hingga melakukan pembayaran,
                    tanpa langkah yang rumit.
                </p>

            </div>

        </article>

        <article class="campaign-card">

            <div class="campaign-body">

                <h3>Tepat Sasaran</h3>

                <p class="campaign-description">
                    Setiap campaign dikelola oleh admin yang
                    memastikan dana yang terkumpul disalurkan sesuai
                    tujuan campaign tersebut.
                </p>

            </div>

        </article>

    </div>

</section>


<!-- =====================================================
     CTA
===================================================== -->

<section class="cta">

    <img src="../assets/images/olek-buzunov-uCHUP_skz2M-unsplash.jpg" alt="">

    <div class="cta-content">

        <p class="section-label" style="color: #ddd;">
            MULAI SEKARANG
        </p>

        <h2>
            Jadi bagian<br>
            dari perubahan.
        </h2>

        <p>
            Pilih campaign yang ingin kamu dukung dan
            rasakan dampaknya.
        </p>

        <a
            href="campaign.php"
            class="cta-button"
        >
            Lihat Campaign
        </a>

    </div>

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