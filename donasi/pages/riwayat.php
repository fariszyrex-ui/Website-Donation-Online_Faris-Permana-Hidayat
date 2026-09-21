<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../config/database.php";
require_once "../auth/cek_login.php";

$id_donatur = $_SESSION["id_donatur"];

// Ambil riwayat donasi milik donatur yang sedang login
$query = $pdo->prepare("
    SELECT
        donasi.id_donasi,
        campaign.judul AS judul_campaign,
        donasi.nominal,
        donasi.metode_pembayaran,
        donasi.status,
        donasi.tanggal_donasi
    FROM donasi
    INNER JOIN campaign
        ON donasi.id_campaign = campaign.id_campaign
    WHERE donasi.id_donatur = ?
    ORDER BY donasi.id_donasi DESC
");

$query->execute([$id_donatur]);

$riwayat = $query->fetchAll(PDO::FETCH_ASSOC);

// Ringkasan singkat, dihitung dari data yang sudah diambil di atas
// (tidak ada query tambahan ke database)
$totalTransaksi = count($riwayat);
$totalNominal = array_sum(array_column($riwayat, "nominal"));

// Dipakai untuk menentukan warna badge status secara dinamis
function kelasStatus($status)
{
    $status = strtolower(trim($status));

    if (in_array($status, ["berhasil", "sukses", "diterima", "success"])) {
        return "status-berhasil";
    }

    if (in_array($status, ["pending", "menunggu", "diproses"])) {
        return "status-pending";
    }

    if (in_array($status, ["gagal", "ditolak", "batal", "dibatalkan"])) {
        return "status-gagal";
    }

    return "status-lain";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Riwayat Donasi</title>

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

        :root {
            --accent: #c9a227;
            --line: #262626;
        }

        body {
            min-height: 100vh;

            padding: 60px 20px;

            background: #000;
            color: #f2f2f2;

            font-family: "Inter", sans-serif;
        }

        .container {
            width: 100%;
            max-width: 1000px;

            margin: 0 auto;
        }

        .eyebrow {
            font-size: 11px;
            font-weight: 500;

            letter-spacing: 0.25em;
            text-transform: uppercase;

            color: #888;

            margin-bottom: 12px;
        }

        h1 {
            font-size: clamp(28px, 4vw, 40px);
            font-weight: 400;

            letter-spacing: -0.01em;
            text-transform: uppercase;

            margin-bottom: 10px;
        }

        .subtitle {
            font-family: "Playfair Display", serif;

            color: #999;

            font-size: 15px;
            line-height: 1.7;

            margin-bottom: 35px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;

            margin-bottom: 40px;
        }

        .stat-card {
            background: #0c0c0c;
            border: 1px solid var(--line);

            padding: 22px 24px;
        }

        .stat-label {
            font-size: 10px;
            letter-spacing: 0.15em;
            text-transform: uppercase;

            color: #888;

            margin-bottom: 10px;
        }

        .stat-value {
            font-size: clamp(20px, 3vw, 26px);
            font-weight: 600;
        }

        .card {
            background: #0c0c0c;
            border: 1px solid var(--line);

            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 640px;

            border-collapse: collapse;
        }

        th,
        td {
            padding: 16px 18px;

            text-align: left;

            border-bottom: 1px solid var(--line);

            font-size: 13px;

            white-space: nowrap;
        }

        th {
            font-size: 10px;
            font-weight: 600;

            letter-spacing: 0.12em;
            text-transform: uppercase;

            color: #888;

            background: #111;
        }

        tbody tr {
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: #131313;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;

            padding: 5px 12px;

            border: 1px solid #444;

            font-size: 10px;
            font-weight: 600;

            letter-spacing: 0.08em;
            text-transform: uppercase;

            color: #ddd;
        }

        .status-berhasil {
            color: #7be08f;
            border-color: rgba(123, 224, 143, 0.4);
            background: rgba(123, 224, 143, 0.08);
        }

        .status-pending {
            color: #e0c37b;
            border-color: rgba(224, 195, 123, 0.4);
            background: rgba(224, 195, 123, 0.08);
        }

        .status-gagal {
            color: #ff9a9a;
            border-color: rgba(255, 90, 90, 0.35);
            background: rgba(255, 90, 90, 0.08);
        }

        .kosong {
            padding: 70px 20px;

            text-align: center;

            color: #777;

            font-size: 14px;
            line-height: 1.7;
        }

        .kosong-icon {
            font-size: 34px;

            margin-bottom: 16px;

            opacity: 0.6;
        }

        .back-link {
            display: inline-block;
            margin-top: 28px;

            font-size: 12px;
            letter-spacing: 0.1em;
            text-transform: uppercase;

            color: #888;
            text-decoration: none;

            transition: color 0.25s ease;
        }

        .back-link:hover {
            color: #fff;
        }

        @media (max-width: 600px) {

            body {
                padding: 40px 16px;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <p class="eyebrow">Akun Saya</p>

    <h1>Riwayat Donasi</h1>

    <p class="subtitle">
        Berikut adalah riwayat donasi yang telah kamu lakukan.
    </p>

    <?php if ($totalTransaksi > 0): ?>

        <div class="stat-grid">

            <div class="stat-card">
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value">
                    <?= $totalTransaksi ?> Donasi
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Total Nominal Didonasikan</div>
                <div class="stat-value">
                    Rp <?= number_format($totalNominal, 0, ",", ".") ?>
                </div>
            </div>

        </div>

    <?php endif; ?>

    <div class="card">

        <?php if (count($riwayat) > 0): ?>

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Campaign</th>
                        <th>Nominal</th>
                        <th>Metode Pembayaran</th>
                        <th>Status</th>
                        <th>Tanggal Donasi</th>
                    </tr>

                </thead>

                <tbody>

                    <?php $no = 1; ?>

                    <?php foreach ($riwayat as $data): ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $data["judul_campaign"]
                                ) ?>
                            </td>

                            <td>
                                Rp <?= number_format(
                                    $data["nominal"],
                                    0,
                                    ",",
                                    "."
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $data["metode_pembayaran"]
                                ) ?>
                            </td>

                            <td>
                                <span class="status-badge <?= kelasStatus($data["status"]) ?>">
                                    <?= htmlspecialchars(
                                        $data["status"]
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $data["tanggal_donasi"]
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="kosong">
                <div class="kosong-icon">🕊️</div>
                Kamu belum memiliki riwayat donasi.
            </div>

        <?php endif; ?>

    </div>

    <a href="../index.php" class="back-link">
        &larr; Kembali ke Beranda
    </a>

</div>

</body>

</html>