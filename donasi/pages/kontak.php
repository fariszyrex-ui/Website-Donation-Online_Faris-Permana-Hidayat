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

    <title>Kontak — Donasi Online</title>

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

            <a href="tentang.php">
                Tentang
            </a>

            <a
                href="kontak.php"
                class="active"
            >
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
     KONTAK
===================================================== -->

<section class="section">

    <div class="intro">

        <div>

            <p class="section-label">
                KONTAK KAMI
            </p>

            <h1 class="section-title" style="margin-top: 15px;">
                Ada yang bisa<br>
                kami bantu?
            </h1>

        </div>

        <p class="intro-text">
            Punya pertanyaan seputar campaign, donasi, atau ingin
            mengajukan campaign baru? Hubungi kami melalui salah satu
            kanal di bawah ini, tim kami akan segera merespons.
        </p>

    </div>

</section>


<section class="section" style="padding-top: 0;">

    <div class="campaign-grid">

        <article class="campaign-card">

            <div class="campaign-body">

                <h3>Email</h3>

                <p class="campaign-description">
                    Kirim pertanyaan atau kerja sama campaign
                    melalui email kami.
                </p>

                <a
                    class="card-button"
                    href="mailto:halo@donasionline.id"
                    style="margin-top: 20px;"
                >
                    halo@donasionline.id
                </a>

            </div>

        </article>

        <article class="campaign-card">

            <div class="campaign-body">

                <h3>Telepon / WhatsApp</h3>

                <p class="campaign-description">
                    Hubungi tim kami untuk respons yang lebih
                    cepat pada jam kerja.
                </p>

                <a
                    class="card-button"
                    href="tel:+6281234567890"
                    style="margin-top: 20px;"
                >
                    +62 812-8755-6659
                </a>

            </div>

        </article>

        <article class="campaign-card">

            <div class="campaign-body">

                <h3>Alamat</h3>

                <p class="campaign-description">
                    Jl.Rancaekek Permai. 10,
                    Bandung, Jawa Barat, Indonesia.
                </p>

                <p class="campaign-description" style="margin-top: 12px;">
                    Senin – Jumat, 09.00 – 17.00 WIB
                </p>

            </div>

        </article>

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