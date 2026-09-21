<?php
session_start();

require_once "../config/database.php";
require_once "../auth/cek_login.php";

// Pastikan data dikirim dari form donasi
if (
    !isset($_POST["id_campaign"]) ||
    !isset($_POST["nominal"]) ||
    !isset($_POST["metode_pembayaran"])
) {
    header("Location: ../index.php");
    exit;
}

$id_campaign = $_POST["id_campaign"];
$nominal = $_POST["nominal"];
$metode = $_POST["metode_pembayaran"];

// Ambil data campaign
$query = $pdo->prepare("
    SELECT *
    FROM campaign
    WHERE id_campaign = ?
");

$query->execute([$id_campaign]);

$campaign = $query->fetch(PDO::FETCH_ASSOC);

if (!$campaign) {
    die("Campaign tidak ditemukan.");
}

// Pastikan campaign masih aktif
if (strtolower($campaign["status"]) !== "aktif") {
    die("Campaign ini sudah ditutup.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pembayaran Donasi</title>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@400;500&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 50px 20px;

            background: #000;
            color: #f2f2f2;

            font-family: "Inter", sans-serif;
        }

        .container {
            width: 100%;
            max-width: 600px;
        }

        .card {
            background: #0c0c0c;
            border: 1px solid #262626;
            padding: 50px 45px;
        }

        .eyebrow {
            font-size: 11px;
            font-weight: 500;

            letter-spacing: 0.25em;
            text-transform: uppercase;

            color: #888;

            margin-bottom: 12px;
        }

        .campaign-title {
            font-size: clamp(22px, 4vw, 28px);
            font-weight: 400;

            letter-spacing: -0.01em;
            text-transform: uppercase;

            margin-bottom: 28px;
        }

        .nominal-label {
            font-size: 11px;
            letter-spacing: 0.15em;
            text-transform: uppercase;

            color: #888;

            margin-bottom: 10px;
        }

        .nominal-value {
            font-size: clamp(30px, 6vw, 38px);
            font-weight: 600;

            margin-bottom: 30px;
        }

        .method-box {
            border: 1px solid #262626;
            padding: 26px;

            margin-bottom: 30px;
        }

        .method-name {
            display: flex;
            align-items: center;
            gap: 10px;

            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;

            margin-bottom: 16px;
        }

        .method-name span {
            font-size: 22px;
        }

        .method-note {
            font-family: "Playfair Display", serif;

            color: #aaa;

            font-size: 14px;
            line-height: 1.7;

            margin-bottom: 16px;
        }

        .rekening-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;

            padding: 14px 16px;

            background: #000;
            border: 1px solid #292929;

            margin-top: 12px;
        }

        .rekening-info div:first-child {
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;

            color: #888;

            margin-bottom: 4px;
        }

        .rekening-info div:last-child {
            font-size: 16px;
            font-weight: 600;

            letter-spacing: 0.03em;
        }

        .copy-btn {
            flex-shrink: 0;

            padding: 8px 14px;

            background: transparent;
            border: 1px solid #444;
            color: #ccc;

            font-family: "Inter", sans-serif;
            font-size: 11px;
            letter-spacing: 0.08em;
            text-transform: uppercase;

            cursor: pointer;

            transition: all 0.25s ease;
        }

        .copy-btn:hover {
            background: #fff;
            color: #000;
            border-color: #fff;
        }

        button[type="submit"] {
            width: 100%;

            padding: 17px;

            background: transparent;
            border: 1px solid #fff;
            color: #fff;

            font-family: "Inter", sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;

            cursor: pointer;

            transition: all 0.3s ease;
        }

        button[type="submit"]:hover {
            background: #fff;
            color: #000;
        }

        .btn-ganti {
            width: 100%;

            padding: 15px;

            margin-top: 12px;

            background: transparent;
            border: 1px solid #333;
            color: #999;

            font-family: "Inter", sans-serif;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.15em;
            text-transform: uppercase;

            cursor: pointer;

            transition: all 0.25s ease;
        }

        .btn-ganti:hover {
            border-color: #666;
            color: #ddd;
        }

        .peringatan {
            font-size: 12px;
            color: #777;

            margin-top: 18px;

            line-height: 1.6;
        }

        .timer-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;

            padding: 14px 16px;

            background: #000;
            border: 1px solid #292929;

            margin-bottom: 22px;
        }

        .timer-label {
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;

            color: #888;
        }

        .timer-value {
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 0.05em;

            font-variant-numeric: tabular-nums;
        }

        .timer-value.expired {
            color: #ff5b5b;
        }

        .qris-image-wrap {
            display: flex;
            justify-content: center;

            background: #fff;
            padding: 20px;

            margin-top: 4px;
            margin-bottom: 8px;
        }

        .qris-image-wrap img {
            width: 100%;
            max-width: 260px;
            height: auto;

            display: block;
        }

        .overlay-expired {
            display: none;

            position: fixed;
            inset: 0;

            background: rgba(0, 0, 0, 0.85);

            align-items: center;
            justify-content: center;

            z-index: 999;

            text-align: center;
            padding: 24px;
        }

        .overlay-expired.show {
            display: flex;
        }

        .overlay-expired-box {
            border: 1px solid #333;
            padding: 40px 30px;
            max-width: 380px;
        }

        .overlay-expired-box h2 {
            font-size: 18px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .overlay-expired-box p {
            font-size: 13px;
            color: #aaa;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .overlay-expired-box a {
            display: inline-block;
            padding: 12px 22px;

            border: 1px solid #fff;
            color: #fff;
            text-decoration: none;

            font-size: 12px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .overlay-expired-box a:hover {
            background: #fff;
            color: #000;
        }

        @media (max-width: 480px) {

            .card {
                padding: 35px 26px;
            }

            .rekening-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .copy-btn {
                width: 100%;
            }

        }

    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <p class="eyebrow">Pembayaran Donasi</p>

        <h1 class="campaign-title">
            <?= htmlspecialchars($campaign["judul"]) ?>
        </h1>

        <div class="nominal-label">Nominal Donasi</div>

        <div class="nominal-value">
            Rp <?= number_format($nominal, 0, ",", ".") ?>
        </div>

        <div class="method-box">

            <div class="timer-box">
                <span class="timer-label">Selesaikan Pembayaran Dalam</span>
                <span class="timer-value" id="countdownTimer">15:00</span>
            </div>

            <?php if ($metode === "QRIS"): ?>

                <div class="method-name">
                    <span>&#9635;</span>
                    QRIS
                </div>

                <p class="method-note">
                    Silakan scan QRIS di bawah ini melalui aplikasi
                    dompet digital atau mobile banking kamu.
                </p>

                <div class="qris-image-wrap">
                    <img src="../assets/images/qris.png" alt="Kode QRIS">
                </div>

            <?php elseif ($metode === "BCA"): ?>

                <div class="method-name">
                    <span>&#127974;</span>
                    BCA
                </div>

                <p class="method-note">
                    Transfer ke rekening BCA di bawah ini sesuai nominal
                    donasi kamu.
                </p>

                <div class="rekening-row">
                    <div class="rekening-info">
                        <div>No. Rekening</div>
                        <div id="bcaNumber">1234567890</div>
                    </div>
                    <button
                        type="button"
                        class="copy-btn"
                        data-copy="1234567890"
                    >
                        Salin
                    </button>
                </div>

                <div class="rekening-row">
                    <div class="rekening-info">
                        <div>Atas Nama</div>
                        <div>Donasi Online</div>
                    </div>
                </div>

            <?php elseif ($metode === "DANA"): ?>

                <div class="method-name">
                    <span>&#128241;</span>
                    DANA
                </div>

                <p class="method-note">
                    Kirim donasi melalui nomor DANA di bawah ini sesuai
                    nominal donasi kamu.
                </p>

                <div class="rekening-row">
                    <div class="rekening-info">
                        <div>Nomor DANA</div>
                        <div id="danaNumber">081234567890</div>
                    </div>
                    <button
                        type="button"
                        class="copy-btn"
                        data-copy="081234567890"
                    >
                        Salin
                    </button>
                </div>

                <div class="rekening-row">
                    <div class="rekening-info">
                        <div>Atas Nama</div>
                        <div>Donasi Online</div>
                    </div>
                </div>

            <?php endif; ?>

        </div>

        <!-- KONFIRMASI PEMBAYARAN -->

        <form action="konfirmasi_donasi.php" method="POST">

            <input
                type="hidden"
                name="id_campaign"
                value="<?= $id_campaign ?>"
            >

            <input
                type="hidden"
                name="nominal"
                value="<?= $nominal ?>"
            >

            <input
                type="hidden"
                name="metode_pembayaran"
                value="<?= htmlspecialchars($metode) ?>"
            >

            <button type="submit">
                Saya Sudah Membayar
            </button>

        </form>

        <button type="button" class="btn-ganti" id="btnGantiMetode">
            Ganti Metode Pembayaran
        </button>

        <p class="peringatan">
            Pastikan kamu sudah menyelesaikan pembayaran sebelum menekan
            tombol "Saya Sudah Membayar".
        </p>

    </div>

</div>

<div class="overlay-expired" id="overlayExpired">
    <div class="overlay-expired-box">
        <h2>Waktu Pembayaran Habis</h2>
        <p>
            Batas waktu untuk menyelesaikan pembayaran donasi ini
            sudah berakhir. Silakan ulangi proses donasi.
        </p>
        <a href="../index.php">Kembali ke Beranda</a>
    </div>
</div>

<script>
(function () {

    const buttons = document.querySelectorAll(".copy-btn");

    buttons.forEach(function (btn) {

        btn.addEventListener("click", function () {

            const text = btn.getAttribute("data-copy");

            navigator.clipboard.writeText(text).then(function () {

                const original = btn.textContent;
                btn.textContent = "Disalin!";

                setTimeout(function () {
                    btn.textContent = original;
                }, 1500);

            });

        });

    });

})();

(function () {

    const btnGanti = document.getElementById("btnGantiMetode");

    if (btnGanti) {
        btnGanti.addEventListener("click", function () {
            // Kembali ke halaman sebelumnya (form donasi) supaya
            // user bisa pilih ulang metode pembayaran.
            if (document.referrer) {
                window.history.back();
            } else {
                window.location.href = "../index.php";
            }
        });
    }

})();

(function () {

    // Durasi timer dalam detik (15 menit).
    // Timer selalu dimulai ulang dari awal setiap halaman ini dimuat,
    // supaya tiap transaksi/percobaan donasi punya batas waktu sendiri
    // dan tidak "nyangkut" kadaluarsa dari percobaan sebelumnya.
    const DURASI_AWAL = 15 * 60;

    const timerEl = document.getElementById("countdownTimer");
    const overlayEl = document.getElementById("overlayExpired");
    const submitBtn = document.querySelector("button[type='submit']");

    const batasWaktu = Date.now() + DURASI_AWAL * 1000;

    function formatWaktu(detik) {
        const m = Math.floor(detik / 60).toString().padStart(2, "0");
        const s = Math.floor(detik % 60).toString().padStart(2, "0");
        return m + ":" + s;
    }

    function tampilkanExpired() {
        timerEl.textContent = "00:00";
        timerEl.classList.add("expired");
        overlayEl.classList.add("show");

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.4";
            submitBtn.style.cursor = "not-allowed";
        }
    }

    function perbaruiTimer() {
        const sisaMs = batasWaktu - Date.now();

        if (sisaMs <= 0) {
            tampilkanExpired();
            clearInterval(interval);
            return;
        }

        const sisaDetik = sisaMs / 1000;
        timerEl.textContent = formatWaktu(sisaDetik);

        if (sisaDetik <= 60) {
            timerEl.classList.add("expired");
        }
    }

    perbaruiTimer();
    const interval = setInterval(perbaruiTimer, 1000);

})();
</script>

</body>

</html>