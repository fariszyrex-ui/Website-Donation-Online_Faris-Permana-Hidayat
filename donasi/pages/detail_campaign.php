<?php

session_start();

require_once "../config/database.php";

if (!isset($_GET["id"])) {
    die("Campaign tidak ditemukan.");
}

$id_campaign = $_GET["id"];

$query = $pdo->prepare("
    SELECT *
    FROM campaign
    WHERE id_campaign = ?
");

$query->execute([$id_campaign]);

$campaign = $query->fetch();

if (!$campaign) {
    die("Campaign tidak ditemukan.");
}

// =====================================================
// PROGRESS DONASI
// =====================================================

$target = (float) $campaign["target_donasi"];
$terkumpul = (float) $campaign["dana_terkumpul"];

$progress = 0;

if ($target > 0) {
    $progress = ($terkumpul / $target) * 100;
}

$progress = min($progress, 100);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= htmlspecialchars($campaign["judul"]) ?> — Donasi Online</title>

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
         EXTRA STYLE — khusus tampilan halaman detail
         (badge status & kotak info tanggal). Tidak menyentuh
         style.css utama.
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

        .detail-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;

            margin-top: 22px;
            padding: 18px;

            border: 1px solid #262626;
            background: #0c0c0c;
        }

        .detail-meta-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .detail-meta-label {
            font-size: 10px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #888;
        }

        .detail-meta-value {
            font-size: 14px;
            color: #eee;
            font-weight: 500;
        }

        @media (max-width: 480px) {

            .detail-meta {
                grid-template-columns: 1fr;
            }

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
     DETAIL CAMPAIGN
===================================================== -->

<section class="section">

    <div class="section-header">

        <div>

            <p class="section-label">
                DETAIL CAMPAIGN
            </p>

            <h1 class="section-title">
                <?= htmlspecialchars($campaign["judul"]) ?>
            </h1>

        </div>

    </div>

    <div class="intro">

        <!-- =========================================
             GAMBAR
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

                <p class="intro-text">
                    Tidak ada gambar
                </p>

            <?php endif; ?>

        </div>


        <!-- =========================================
             INFO
        ========================================== -->

        <div>

            <span class="status-badge <?= strtolower(trim($campaign["status"])) === "aktif" ? "" : "closed" ?>">
                <?= strtoupper(htmlspecialchars($campaign["status"])) ?>
            </span>

            <p class="campaign-description">
                <?= nl2br(htmlspecialchars($campaign["deskripsi"])) ?>
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

            <div class="progress-label">

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

          <?php if (strtolower(trim($campaign["status"])) === "aktif"): ?>

    <div class="deadline"
         data-deadline="<?= htmlspecialchars($campaign["tanggal_batas"]) ?>">
        ⏳ Memuat waktu...
    </div>

<?php else: ?>

    <div class="closed">
        🔒 Campaign telah ditutup
    </div>

<?php endif; ?>


            <!-- =====================================
                 META
            ====================================== -->

            <div class="detail-meta">

                <div class="detail-meta-item">
                    <span class="detail-meta-label">Tanggal Mulai</span>
                    <span class="detail-meta-value">
                        <?= htmlspecialchars($campaign["tanggal_mulai"]) ?>
                    </span>
                </div>

                <div class="detail-meta-item">
                    <span class="detail-meta-label">Batas Donasi</span>
                    <span class="detail-meta-value">
                        <?= htmlspecialchars($campaign["tanggal_batas"]) ?>
                    </span>
                </div>

            </div>


            <!-- =====================================
                 CTA
            ====================================== -->

            <?php if (strtolower($campaign["status"]) === "aktif"): ?>

                <?php if (isset($_SESSION["login"]) && $_SESSION["login"] === true && $_SESSION["role"] === "donatur"): ?>

                    <a
                        href="donasi.php?id=<?= $campaign["id_campaign"] ?>"
                        class="card-button"
                    >
                        Donasi Sekarang
                    </a>

                <?php else: ?>

                    <a
                        href="../auth/auth.php"
                        class="card-button"
                    >
                        Login untuk Berdonasi
                    </a>

                <?php endif; ?>

            <?php else: ?>

                <p class="intro-text">
                    Campaign ini sudah ditutup.
                </p>

            <?php endif; ?>

        </div>

    </div>

    <a
        href="campaign.php"
        class="section-link"
    >
        ← Kembali ke Campaign
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


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script src="../assets/js/script.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    function updateDetailCountdown() {

        const countdowns = document.querySelectorAll(
            ".detail-countdown"
        );

        countdowns.forEach(function (element) {

            const deadlineText = element.getAttribute("data-deadline");

            if (!deadlineText) {
                element.innerHTML = "⚠️ Deadline belum tersedia";
                return;
            }

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
                return;
            }

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

    updateDetailCountdown();

    setInterval(
        updateDetailCountdown,
        1000
    );

});
</script>
</body>

</html>