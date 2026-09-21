<?php

session_start();

require_once "../config/database.php";
require_once "../functions/fungsi_umum.php";



// =====================================================
// QUERY CAMPAIGN
// =====================================================

$query = $pdo->prepare("
    SELECT *
    FROM campaign
    ORDER BY id_campaign DESC
");
$query->execute();

$campaigns = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Campaign — Donasi Online</title>

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

    <!-- =====================================================
         EXTRA STYLE — khusus elemen dinamis halaman Campaign
         yang belum ada di style.css (badge status, no-image,
         state "ditutup"). Tidak menyentuh style.css utama.
    ===================================================== -->
    <style>

        .status-badge {
            display: inline-block;

            padding: 4px 12px;
            margin-bottom: 14px;

            border: 1px solid #444;

            font-size: 10px;
            letter-spacing: 0.15em;
            text-transform: uppercase;

            color: #ddd;
        }

        .status-badge.closed {
            color: #777;
            border-color: #292929;
        }

        .campaign-no-image {
            display: flex;
            align-items: center;
            justify-content: center;

            height: 100%;
            min-height: 160px;

            background: #111;
            color: #555;

            font-size: 12px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .deadline.closed {
            color: #666;
            border: 1px solid #292929;
        }

        .empty-campaign {
            text-align: center;
            padding: 60px 0;
        }

        .empty-campaign .intro-text {
            margin: 20px auto 0;
            max-width: 480px;
        }

    </style>

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

            <a
                href="campaign.php"
                class="active"
            >
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
     HERO / INTRO
===================================================== -->

<section class="section" style="padding-top: 170px; padding-bottom: 60px;">

    <div class="intro">

        <div>

            <p class="section-label">
                DONASI ONLINE
            </p>

            <h1 class="section-title" style="margin-top: 15px;">
                Pilih perjuangan,<br>
                mulai perubahan.
            </h1>

        </div>

        <p class="intro-text">
            Temukan campaign yang membutuhkan dukunganmu.
            Setiap donasi membawa harapan baru bagi mereka
            yang membutuhkan.
        </p>

    </div>

</section>


<!-- =====================================================
     CAMPAIGN
===================================================== -->

<section class="section" style="padding-top: 0;">

    <?php if (count($campaigns) > 0): ?>

        <div class="section-header">

            <div>

                <p class="section-label">
                    PILIH PERJUANGAN
                </p>

                <h2 class="section-title">
                    Semua Campaign
                </h2>

            </div>

        </div>

        <div class="campaign-grid">

            <?php foreach ($campaigns as $campaign): ?>
<?php
$status = strtolower(trim($campaign["status"]));
?>
                <?php

                // =================================================
                // PROGRESS DONASI
                // =================================================

                $target = (float) $campaign["target_donasi"];
                $terkumpul = (float) $campaign["dana_terkumpul"];

                $progress = 0;

                if ($target > 0) {
                    $progress = ($terkumpul / $target) * 100;
                }

                $progress = min($progress, 100);

                ?>

                <article class="campaign-card">

                    <!-- =========================================
                         IMAGE
                    ========================================== -->

                    <div class="campaign-image">

                        <div class="campaign-badge">
                            <?= number_format($progress, 0) ?>% Terkumpul
                        </div>

                        <?php if (!empty($campaign["gambar"])): ?>

                            <img
                                src="../uploads/campaign/<?= htmlspecialchars($campaign["gambar"]) ?>"
                                alt="<?= htmlspecialchars($campaign["judul"]) ?>"
                            >

                        <?php else: ?>

                            <div class="campaign-no-image">
                                Tidak ada gambar
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- =========================================
                         CONTENT
                    ========================================== -->

                    <div class="campaign-body">


                        <!-- STATUS -->

                        <?php if ($status === "aktif"): ?>

    <span class="status-badge">
        AKTIF
    </span>

<?php else: ?>

    <span class="status-badge closed">
        DITUTUP
    </span>

<?php endif; ?>


                        <!-- TITLE -->

                        <h3>
                            <?= htmlspecialchars(
                                $campaign["judul"]
                            ) ?>
                        </h3>


                        <!-- DESCRIPTION -->

                        <p class="campaign-description">
                            <?= htmlspecialchars(
                                substr(
                                    $campaign["deskripsi"],
                                    0,
                                    150
                                )
                            ) ?>...
                        </p>


                        <!-- =====================================
                             PROGRESS
                        ====================================== -->

                        <div class="campaign-progress">

                            <div class="progress-label">

                                <span>
                                    Rp <?= number_format(
                                        $terkumpul,
                                        0,
                                        ',',
                                        '.'
                                    ) ?> terkumpul
                                </span>

                                <span>
                                    <?= number_format(
                                        $progress,
                                        0
                                    ) ?>%
                                </span>

                            </div>

                            <div class="progress-bar">

                                <div
                                    class="progress-fill"
                                    style="width: <?= $progress ?>%;"
                                ></div>

                            </div>

                        </div>

                        <div
                            class="progress-label"
                            style="margin-top: 12px;"
                        >

                            <span>
                                Target
                            </span>

                            <strong>
                                Rp <?= number_format(
                                    $target,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </strong>

                        </div>


                        <!-- =====================================
                             DEADLINE
                        ====================================== -->

                       <?php if ($status === "aktif"): ?>

    <div
        class="deadline campaign-page-countdown"
        data-deadline="<?= date('Y-m-d\TH:i:s', strtotime($campaign["tanggal_batas"])) ?>"
    >
        ⏳ Memuat waktu...
    </div>

<?php else: ?>

    <div class="closed">
        🔒 Campaign telah ditutup
    </div>

<?php endif; ?>


                        <!-- =====================================
                             DETAIL BUTTON
                        ====================================== -->

                        <a
                            href="detail_campaign.php?id=<?= (int) $campaign["id_campaign"] ?>"
                            class="card-button"
                        >
                            Lihat Campaign
                            <span>→</span>
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty-campaign">

            <p class="section-label">
                CAMPAIGN
            </p>

            <h2 class="section-title">
                Belum ada campaign.
            </h2>

            <p class="intro-text">
                Campaign donasi akan muncul di halaman ini.
            </p>

        </div>

    <?php endif; ?>

</section>


<!-- =====================================================
     CTA
===================================================== -->

<section class="cta">

    <img
        src="../assets/images/olek-buzunov-uCHUP_skz2M-unsplash.jpg"
        alt=""
    >

    <div class="cta-content">

        <p class="section-label" style="color: #ddd;">
            SATU LANGKAH
        </p>

        <h2>
            Satu donasi.<br>
            Satu harapan.
        </h2>

        <p>
            Dukungan kecilmu dapat berarti besar
            bagi mereka yang membutuhkan.
        </p>

        <a
            href="../index.php"
            class="cta-button"
        >
            Kembali ke Beranda
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

                <p
                    class="intro-text"
                    style="font-size: 14px; color: #999; margin-top: 15px; max-width: 320px;"
                >
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


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script src="../assets/js/script.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    function updateCampaignPageCountdown() {

        const countdowns = document.querySelectorAll(
            ".campaign-page-countdown"
        );

        countdowns.forEach(function (element) {

            const deadlineText = element.getAttribute("data-deadline");

            if (!deadlineText) {
                element.innerHTML = "⚠️ Deadline belum tersedia";
                return;
            }

            // Ambil tanggal dari database
            const deadline = new Date(
                deadlineText.replace(" ", "T")
            );

            const now = new Date();

            const distance = deadline.getTime() - now.getTime();

            if (isNaN(deadline.getTime())) {
                element.innerHTML = "⚠️ Format deadline tidak valid";
                return;
            }

            if (distance <= 0) {
                element.innerHTML = "🔒 Campaign telah ditutup";
                element.classList.add("closed");
                return;
            }

            element.classList.remove("closed");

            const days = Math.floor(
                distance / (1000 * 60 * 60 * 24)
            );

            const hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) /
                (1000 * 60 * 60)
            );

            const minutes = Math.floor(
                (distance % (1000 * 60 * 60)) /
                (1000 * 60)
            );

            const seconds = Math.floor(
                (distance % (1000 * 60)) /
                1000
            );

            if (days > 0) {

                element.innerHTML =
                    "⏳ " +
                    days +
                    " hari lagi sebelum ditutup";

            } else {

                element.innerHTML =
                    "⚡ " +
                    hours +
                    "j " +
                    minutes +
                    "m " +
                    seconds +
                    "d lagi";

            }

        });
    }

    updateCampaignPageCountdown();

    setInterval(
        updateCampaignPageCountdown,
        1000
    );

});
</script>
</body>

</html>