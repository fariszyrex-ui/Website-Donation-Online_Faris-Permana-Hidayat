<?php
session_start();

require_once "../config/database.php";
require_once "../auth/cek_login.php";

// Cek ID campaign
if (!isset($_GET["id"])) {
    die("Campaign tidak ditemukan.");
}

$id_campaign = $_GET["id"];

// Ambil data campaign
$query = $pdo->prepare("SELECT * FROM campaign WHERE id_campaign = ?");
$query->execute([$id_campaign]);

$campaign = $query->fetch(PDO::FETCH_ASSOC);

if (!$campaign) {
    die("Campaign tidak ditemukan.");
}

// Cek deadline campaign
// Campaign harus aktif untuk melakukan donasi
if (strtolower($campaign["status"]) !== "aktif") {
    die("Campaign ini sudah ditutup.");
}
// Cek status campaign
if (strtolower($campaign["status"]) !== "aktif") {
    die("Campaign ini sudah ditutup.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Donasi - <?= htmlspecialchars($campaign["judul"]) ?></title>

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
            max-width: 640px;
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
            font-size: clamp(24px, 4vw, 32px);
            font-weight: 400;

            letter-spacing: -0.01em;
            text-transform: uppercase;

            margin-bottom: 32px;
        }

        .campaign-desc {
            font-family: "Playfair Display", serif;

            color: #b3b3b3;

            font-size: 15px;
            line-height: 1.8;

            margin-bottom: 30px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;

            padding: 20px 0;

            border-top: 1px solid #222;
            border-bottom: 1px solid #222;

            margin-bottom: 35px;
        }

        .stat-label {
            font-size: 10px;
            letter-spacing: 0.15em;
            text-transform: uppercase;

            color: #888;

            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 600;
        }

        label.field-label {
            display: block;

            font-size: 11px;
            font-weight: 600;

            letter-spacing: 0.15em;
            text-transform: uppercase;

            color: #ccc;

            margin-top: 26px;
            margin-bottom: 12px;
        }

        .quick-amounts {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;

            margin-bottom: 14px;
        }

        .quick-amount {
            padding: 9px 16px;

            background: transparent;
            border: 1px solid #333;
            color: #ccc;

            font-family: "Inter", sans-serif;
            font-size: 12px;
            letter-spacing: 0.03em;

            cursor: pointer;

            transition: all 0.25s ease;
        }

        .quick-amount:hover,
        .quick-amount.active {
            background: #fff;
            color: #000;
            border-color: #fff;
        }

        input[type="text"] {
            width: 100%;

            padding: 15px 16px;

            background: #000;
            border: 1px solid #333;
            color: #fff;

            font-family: "Inter", sans-serif;
            font-size: 17px;

            outline: none;

            transition: border-color 0.25s ease;
        }

        input[type="text"]::placeholder {
            color: #555;
        }

        input[type="text"]:focus {
            border-color: #fff;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .payment-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .payment-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;

            padding: 20px 10px;

            border: 1px solid #333;

            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;

            color: #ccc;

            cursor: pointer;

            transition: all 0.25s ease;
        }

        .payment-option label span {
            font-size: 20px;
        }

        .payment-option input:checked + label {
            background: #fff;
            color: #000;
            border-color: #fff;
        }

        button[type="submit"] {
            width: 100%;
            margin-top: 35px;

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

        .back-link {
            display: inline-block;
            margin-top: 24px;

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

        @media (max-width: 480px) {

            .card {
                padding: 35px 26px;
            }

            .payment-grid {
                gap: 8px;
            }

            .payment-option label {
                padding: 14px 6px;
                font-size: 10px;
            }

        }

    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <p class="eyebrow">Form Donasi</p>

        <h1 class="campaign-title">
            <?= htmlspecialchars($campaign["judul"]) ?>
        </h1>

        <div class="stat-grid">

            <div>
                <div class="stat-label">Terkumpul</div>
                <div class="stat-value">
                    Rp <?= number_format($campaign["dana_terkumpul"], 0, ",", ".") ?>
                </div>
            </div>

            <div>
                <div class="stat-label">Target</div>
                <div class="stat-value">
                    Rp <?= number_format($campaign["target_donasi"], 0, ",", ".") ?>
                </div>
            </div>

        </div>

        <form
            action="pembayaran.php"
            method="POST"
            id="formDonasi"
        >

            <input
                type="hidden"
                name="id_campaign"
                value="<?= $campaign["id_campaign"] ?>"
            >

            <label class="field-label">Nominal Donasi</label>

            <div class="quick-amounts">
                <button type="button" class="quick-amount" data-amount="25000">Rp 25rb</button>
                <button type="button" class="quick-amount" data-amount="50000">Rp 50rb</button>
                <button type="button" class="quick-amount" data-amount="100000">Rp 100rb</button>
                <button type="button" class="quick-amount" data-amount="250000">Rp 250rb</button>
                <button type="button" class="quick-amount" data-amount="500000">Rp 500rb</button>
            </div>

            <input
                type="text"
                id="nominalDisplay"
                inputmode="numeric"
                placeholder="Contoh: 50.000"
                autocomplete="off"
            >

            <input
                type="hidden"
                name="nominal"
                id="nominalRaw"
            >

            <label class="field-label">Metode Pembayaran</label>

            <div class="payment-grid">

                <div class="payment-option">
                    <input
                        type="radio"
                        name="metode_pembayaran"
                        id="metode-qris"
                        value="QRIS"
                        required
                    >
                    <label for="metode-qris">
                        <span>&#9635;</span>
                        QRIS
                    </label>
                </div>

                <div class="payment-option">
                    <input
                        type="radio"
                        name="metode_pembayaran"
                        id="metode-bca"
                        value="BCA"
                        required
                    >
                    <label for="metode-bca">
                        <span>&#127974;</span>
                        BCA
                    </label>
                </div>

                <div class="payment-option">
                    <input
                        type="radio"
                        name="metode_pembayaran"
                        id="metode-dana"
                        value="DANA"
                        required
                    >
                    <label for="metode-dana">
                        <span>&#128241;</span>
                        DANA
                    </label>
                </div>

            </div>

            <button type="submit">
                Lanjut ke Pembayaran
            </button>

        </form>

        <a
            href="detail_campaign.php?id=<?= $campaign["id_campaign"] ?>"
            class="back-link"
        >
            &larr; Kembali ke Campaign
        </a>

    </div>

</div>

<script>
(function () {

    const display = document.getElementById("nominalDisplay");
    const raw = document.getElementById("nominalRaw");
    const chips = document.querySelectorAll(".quick-amount");
    const form = document.getElementById("formDonasi");

    function formatRupiah(digits) {
        if (!digits) return "";
        return new Intl.NumberFormat("id-ID").format(digits);
    }

    function setNominal(digits) {
        raw.value = digits;
        display.value = formatRupiah(digits);

        chips.forEach(function (chip) {
            chip.classList.toggle(
                "active",
                chip.dataset.amount === digits
            );
        });
    }

    display.addEventListener("input", function () {
        const digits = display.value.replace(/\D/g, "");
        setNominal(digits);
    });

    chips.forEach(function (chip) {
        chip.addEventListener("click", function () {
            setNominal(chip.dataset.amount);
        });
    });

    form.addEventListener("submit", function (e) {

        const value = parseInt(raw.value || "0", 10);

        if (!value || value < 1000) {
            e.preventDefault();
            alert("Masukkan nominal donasi minimal Rp 1.000.");
            display.focus();
        }

    });

})();
</script>

</body>

</html>