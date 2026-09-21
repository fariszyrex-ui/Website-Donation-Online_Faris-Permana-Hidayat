<?php
session_start();

require_once "../config/database.php";
require_once "../auth/cek_login.php";

// Pastikan data dikirim
if (
    !isset($_POST["id_campaign"]) ||
    !isset($_POST["nominal"]) ||
    !isset($_POST["metode_pembayaran"])
) {
    header("Location: ../index.php");
    exit;
}

$id_donatur = $_SESSION["id_donatur"];
$id_campaign = $_POST["id_campaign"];
$nominal = $_POST["nominal"];
$metode = $_POST["metode_pembayaran"];

// Validasi nominal
if ($nominal <= 0) {
    die("Nominal donasi tidak valid.");
}

// Cek campaign
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

// Cek status campaign
if (strtolower($campaign["status"]) !== "aktif") {
    die("Campaign ini sudah ditutup.");
}

try {

    // Mulai transaksi database
    $pdo->beginTransaction();

    // Simpan data donasi
    $query = $pdo->prepare("
        INSERT INTO donasi
        (
            id_donatur,
            id_campaign,
            nominal,
            metode_pembayaran,
            status,
            tanggal_donasi
        )
        VALUES (?, ?, ?, ?, 'berhasil', NOW())
    ");

    $query->execute([
        $id_donatur,
        $id_campaign,
        $nominal,
        $metode
    ]);

    // Tambahkan dana terkumpul
    $query = $pdo->prepare("
        UPDATE campaign
        SET dana_terkumpul = dana_terkumpul + ?
        WHERE id_campaign = ?
    ");

$query->execute([
    $nominal,
    $id_campaign
]);

// Cek apakah target campaign sudah tercapai
$query = $pdo->prepare("
    UPDATE campaign
    SET status = 'ditutup'
    WHERE id_campaign = ?
    AND dana_terkumpul >= target_donasi
");

$query->execute([
    $id_campaign
]);

// Simpan perubahan
$pdo->commit();
} catch (Exception $e) {

    // Batalkan jika terjadi error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die("Terjadi kesalahan: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Donasi Berhasil</title>

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
            max-width: 560px;
        }

        .card {
            background: #0c0c0c;
            border: 1px solid #262626;
            padding: 55px 45px;

            text-align: center;
        }

        .berhasil {
            width: 64px;
            height: 64px;

            margin: 0 auto 26px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #fff;
            border-radius: 50%;

            font-size: 28px;
        }

        .eyebrow {
            font-size: 11px;
            font-weight: 500;

            letter-spacing: 0.25em;
            text-transform: uppercase;

            color: #888;

            margin-bottom: 10px;
        }

        h1 {
            font-size: clamp(24px, 4vw, 30px);
            font-weight: 400;

            letter-spacing: -0.01em;
            text-transform: uppercase;

            margin-bottom: 18px;
        }

        .terima-kasih {
            font-family: "Playfair Display", serif;

            color: #b3b3b3;

            font-size: 15px;
            line-height: 1.7;

            margin-bottom: 30px;
        }

        .campaign-box {
            padding: 24px;

            border-top: 1px solid #222;
            border-bottom: 1px solid #222;

            margin-bottom: 30px;
        }

        .campaign-label {
            font-size: 10px;
            letter-spacing: 0.15em;
            text-transform: uppercase;

            color: #888;

            margin-bottom: 8px;
        }

        .campaign-name {
            font-size: 17px;
            font-weight: 600;

            margin-bottom: 22px;
        }

        .nominal {
            font-size: clamp(30px, 6vw, 38px);
            font-weight: 600;

            margin-bottom: 18px;
        }

        .metode {
            font-size: 13px;
            color: #999;

            letter-spacing: 0.03em;
        }

        .metode strong {
            color: #f2f2f2;
        }

        .catatan {
            font-size: 13px;
            color: #888;

            line-height: 1.7;

            margin-bottom: 30px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 16px 32px;

            background: transparent;
            border: 1px solid #fff;
            color: #fff;

            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;

            text-decoration: none;

            transition: all 0.3s ease;
        }

        .button:hover {
            background: #fff;
            color: #000;
        }

        @media (max-width: 480px) {

            .card {
                padding: 40px 26px;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="card">

        <div class="berhasil">
            ✓
        </div>

        <p class="eyebrow">Donasi Berhasil</p>

        <h1>Terima Kasih</h1>

        <p class="terima-kasih">
            Terima kasih telah memberikan donasi. Kebaikanmu
            membawa harapan baru bagi mereka yang membutuhkan.
        </p>

        <div class="campaign-box">

            <div class="campaign-label">Campaign</div>

            <div class="campaign-name">
                <?= htmlspecialchars($campaign["judul"]) ?>
            </div>

            <div class="nominal">
                Rp <?= number_format(
                    $nominal,
                    0,
                    ",",
                    "."
                ) ?>
            </div>

            <div class="metode">
                Metode Pembayaran:
                <strong><?= htmlspecialchars($metode) ?></strong>
            </div>

        </div>

        <p class="catatan">
            Donasi kamu telah tercatat dalam sistem.
        </p>

        <a
            class="button"
            href="../index.php"
        >
            Kembali ke Beranda
        </a>

    </div>

</div>

</body>

</html>