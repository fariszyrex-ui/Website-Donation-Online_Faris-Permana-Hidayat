<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../config/database.php";
require_once "../auth/cek_admin.php";
/* =========================
   ESCAPE OUTPUT
========================= */

if (!function_exists("e")) {
    function e($nilai)
    {
        return htmlspecialchars((string) $nilai, ENT_QUOTES, "UTF-8");
    }
}


/* =========================
   FORMAT
========================= */

if (!function_exists("rupiah")) {
    function rupiah($angka)
    {
        return "Rp " . number_format((float) $angka, 0, ",", ".");
    }
}

if (!function_exists("tgl_indo")) {
    function tgl_indo($tanggal)
    {
        if (empty($tanggal)) {
            return "-";
        }

        $ts = strtotime($tanggal);

        if (!$ts) {
            return "-";
        }

        $bulan = [
            1 => "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
            "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
        ];

        return date("j", $ts) . " " . $bulan[(int) date("n", $ts)] . " " . date("Y", $ts);
    }
}

/* Persentase pendanaan (tidak dibatasi 100 agar kelebihan tetap terlihat) */
if (!function_exists("persen")) {
    function persen($terkumpul, $target)
    {
        $target = (float) $target;

        if ($target <= 0) {
            return 0;
        }

        return (int) floor(((float) $terkumpul / $target) * 100);
    }
}

if (!function_exists("sisa_hari")) {
    function sisa_hari($tanggal_batas)
    {
        $batas = strtotime($tanggal_batas . " 23:59:59");

        if (!$batas) {
            return "";
        }

        $selisih = $batas - time();

        if ($selisih < 0) {
            return "Sudah lewat batas";
        }

        return ceil($selisih / 86400) . " hari lagi";
    }
}


/* =========================
   FLASH MESSAGE
========================= */

if (!function_exists("set_flash")) {
    function set_flash($tipe, $pesan)
    {
        $_SESSION["flash"] = ["tipe" => $tipe, "pesan" => $pesan];
    }
}

if (!function_exists("get_flash")) {
    function get_flash()
    {
        if (empty($_SESSION["flash"])) {
            return null;
        }

        $flash = $_SESSION["flash"];
        unset($_SESSION["flash"]);

        return $flash;
    }
}

if (!function_exists("tampilkan_flash")) {
    function tampilkan_flash($flash)
    {
        if (!$flash) {
            return;
        }

        $kelas = $flash["tipe"] === "success" ? "alert-success" : "alert-error";
        $auto  = $flash["tipe"] === "success" ? " data-autohide" : "";

        echo '<div class="alert ' . $kelas . '" role="status"' . $auto . '>'
            . '<span>' . e($flash["pesan"]) . '</span>'
            . '</div>';
    }
}



// ==========================
// TAMBAH CAMPAIGN
// ==========================
if (isset($_POST["tambah"])) {

    $judul         = trim($_POST["judul"]);
    $deskripsi     = trim($_POST["deskripsi"]);
    $target_donasi = $_POST["target_donasi"];
    $tanggal_mulai = $_POST["tanggal_mulai"];
    $tanggal_batas = $_POST["tanggal_batas"];

    $gambar = null;
    $error  = null;

    if ($tanggal_batas < $tanggal_mulai) {
        $error = "Tanggal batas tidak boleh lebih awal dari tanggal mulai.";
    }

    // Upload gambar
    if (!$error && isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] === 0) {

        $nama_file = $_FILES["gambar"]["name"];
        $tmp_file  = $_FILES["gambar"]["tmp_name"];

        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        $ekstensi_diizinkan = ["jpg", "jpeg", "png", "webp"];

        if (!in_array($ekstensi, $ekstensi_diizinkan)) {

            $error = "Format gambar tidak diizinkan. Gunakan JPG, PNG, atau WEBP.";

        } else {

            $nama_baru = uniqid() . "." . $ekstensi;

            $folder = "../uploads/campaign/";

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            move_uploaded_file($tmp_file, $folder . $nama_baru);

            $gambar = $nama_baru;
        }
    }

    if ($error) {
        set_flash("error", $error);
        header("Location: kelola_campaign.php");
        exit;
    }

    $query = $pdo->prepare("
        INSERT INTO campaign
        (
            id_admin,
            judul,
            deskripsi,
            target_donasi,
            dana_terkumpul,
            gambar,
            tanggal_mulai,
            tanggal_batas,
            status
        )
        VALUES (?, ?, ?, ?, 0, ?, ?, ?, 'aktif')
    ");

    $query->execute([
        $_SESSION["id_admin"],
        $judul,
        $deskripsi,
        $target_donasi,
        $gambar,
        $tanggal_mulai,
        $tanggal_batas
    ]);

    set_flash("success", "Campaign berhasil ditambahkan.");
    header("Location: kelola_campaign.php");
    exit;
}


// ==========================
// HAPUS CAMPAIGN
// ==========================
if (isset($_GET["hapus"])) {

    $id_campaign = $_GET["hapus"];

    try {

        $query = $pdo->prepare(
            "DELETE FROM campaign WHERE id_campaign = ?"
        );

        $query->execute([$id_campaign]);

        set_flash("success", "Campaign berhasil dihapus.");

    } catch (PDOException $e) {

        set_flash(
            "error",
            "Campaign tidak bisa dihapus karena masih terhubung dengan data lain, seperti donasi atau berita."
        );
    }

    header("Location: kelola_campaign.php");
    exit;
}


// ==========================
// AMBIL DATA CAMPAIGN
// ==========================
$query = $pdo->prepare("
    SELECT *
    FROM campaign
    ORDER BY id_campaign DESC
");

$query->execute();

$campaigns = $query->fetchAll(PDO::FETCH_ASSOC);

$flash = get_flash();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Campaign</title>

    <style>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap");

/* =========================================================
   TOKEN
========================================================= */
:root {
    --bg: #f5f6f8;
    --surface: #ffffff;
    --ink: #0f172a;
    --ink-2: #334155;
    --muted: #64748b;
    --line: #e5e7eb;
    --line-strong: #cbd5e1;

    --brand: #1e3a5f;
    --brand-strong: #152b47;
    --brand-soft: #eef2f7;

    --ok: #166534;
    --ok-soft: #edf7f0;
    --ok-line: #cfe6d7;
    --danger: #b42318;
    --danger-soft: #fdf1ef;
    --danger-line: #f1c3bd;
    --warn: #92400e;

    --r-panel: 10px;
    --r-control: 8px;

    --font: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
}

*,
*::before,
*::after {
    box-sizing: border-box;
}

[hidden] {
    display: none !important;
}

body {
    background: var(--bg);
    color: var(--ink);
    font-family: var(--font);
    font-size: 14.5px;
    line-height: 1.55;
    -webkit-font-smoothing: antialiased;
}

h1, h2, h3, p {
    margin: 0;
}

a {
    color: var(--brand);
}

:focus-visible {
    outline: 2px solid var(--brand);
    outline-offset: 2px;
}

/* =========================================================
   LAYOUT
========================================================= */
.main {
    padding: 32px 40px 64px;
}

.wrap {
    max-width: 1180px;
}

.page-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}

.page-head h1 {
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -0.015em;
    line-height: 1.3;
}

.page-head p {
    margin-top: 4px;
    color: var(--muted);
}

.page-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

/* =========================================================
   PANEL
========================================================= */
.panel {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--r-panel);
    overflow: hidden;
}

.stats + .panel {
    margin-top: 24px;
}

.panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 22px;
    border-bottom: 1px solid var(--line);
}

.panel-head h2 {
    font-size: 15.5px;
    font-weight: 700;
}

.panel-head a {
    font-weight: 600;
    font-size: 13.5px;
    text-decoration: none;
}

.panel-head a:hover {
    text-decoration: underline;
}

/* =========================================================
   STATISTIK
========================================================= */
.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: var(--line);
    border: 1px solid var(--line);
    border-radius: var(--r-panel);
    overflow: hidden;
}

.stat {
    padding: 20px 24px 22px;
    background: var(--surface);
}

.stat-label {
    color: var(--muted);
    font-size: 13px;
    font-weight: 500;
}

.stat-value {
    margin-top: 6px;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.2;
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
}

.stat-note {
    margin-top: 6px;
    font-size: 12.5px;
    color: var(--muted);
}

/* =========================================================
   TOOLBAR (cari + filter)
========================================================= */
.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 14px 22px;
    border-bottom: 1px solid var(--line);
}

.segmented {
    display: inline-flex;
    padding: 3px;
    gap: 2px;
    border-radius: var(--r-control);
    background: #eef1f5;
}

.segmented button {
    height: 30px;
    padding: 0 14px;
    border: 0;
    border-radius: 6px;
    background: transparent;
    color: var(--muted);
    font: 600 13px var(--font);
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}

.segmented button:hover {
    color: var(--ink);
}

.segmented button.active {
    background: #fff;
    color: var(--ink);
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1);
}

.search {
    width: 260px;
    max-width: 100%;
    height: 38px;
    padding: 0 12px;
    border: 1px solid var(--line-strong);
    border-radius: var(--r-control);
    background: #fff;
    color: var(--ink);
    font: 400 14px var(--font);
    transition: border-color 0.15s, box-shadow 0.15s;
}

.search:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px var(--brand-soft);
}

.no-result {
    padding: 40px 22px;
    text-align: center;
    color: var(--muted);
}

/* =========================================================
   TABEL
========================================================= */
.table-wrap {
    overflow-x: auto;
}

table.data {
    width: 100%;
    min-width: 760px;
    border-collapse: collapse;
}

table.data th {
    padding: 11px 22px;
    background: #f8fafc;
    border-bottom: 1px solid var(--line);
    color: var(--muted);
    font-size: 12.5px;
    font-weight: 600;
    text-align: left;
    white-space: nowrap;
}

table.data td {
    padding: 16px 22px;
    border-bottom: 1px solid var(--line);
    vertical-align: middle;
}

table.data tbody tr:last-child td {
    border-bottom: 0;
}

table.data tbody tr:hover td {
    background: #fafbfc;
}

table.data th.right {
    text-align: right;
}

.cell-media {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 260px;
}

.thumb {
    width: 64px;
    height: 48px;
    flex: none;
    border-radius: 6px;
    object-fit: cover;
    background: var(--brand-soft);
    color: var(--brand);
    font-size: 17px;
    font-weight: 700;
    display: grid;
    place-items: center;
}

.cell-title {
    font-weight: 600;
    color: var(--ink);
}

.cell-desc {
    margin-top: 2px;
    font-size: 13px;
    color: var(--muted);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.muted {
    color: var(--muted);
}

.small {
    font-size: 13px;
}

.tag {
    display: inline-block;
    max-width: 240px;
    padding: 3px 9px;
    border: 1px solid var(--line);
    border-radius: 6px;
    background: #f8fafc;
    color: var(--ink-2);
    font-size: 13px;
    font-weight: 500;
}

/* Progress */
.progress-cell {
    min-width: 220px;
}

.progress {
    height: 6px;
    border-radius: 99px;
    background: #e8ebf0;
    overflow: hidden;
}

.progress > span {
    display: block;
    height: 100%;
    border-radius: inherit;
    background: var(--brand);
    transform-origin: left center;
    animation: grow 1s cubic-bezier(0.2, 0.7, 0.2, 1) both;
}

.progress.done > span {
    background: var(--ok);
}

.progress-meta {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 8px;
    font-size: 13px;
    font-variant-numeric: tabular-nums;
}

.progress-meta strong {
    font-weight: 600;
}

@keyframes grow {
    from {
        transform: scaleX(0);
    }
}

/* Badge */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 3px 10px;
    border: 1px solid transparent;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    white-space: nowrap;
}

.badge::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

.badge-aktif {
    background: var(--ok-soft);
    border-color: var(--ok-line);
    color: var(--ok);
}

.badge-tutup {
    background: #f1f5f9;
    border-color: #e2e8f0;
    color: #475569;
}

.actions {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 8px;
}

/* =========================================================
   TOMBOL
========================================================= */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    height: 40px;
    padding: 0 18px;
    border: 1px solid transparent;
    border-radius: var(--r-control);
    font: 600 14px var(--font);
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
}

.btn-sm {
    height: 32px;
    padding: 0 12px;
    font-size: 13px;
}

.btn-primary {
    background: var(--brand);
    color: #fff;
}

.btn-primary:hover {
    background: var(--brand-strong);
}

.btn-ghost,
.btn-warn-soft,
.btn-danger-soft {
    background: #fff;
    border-color: var(--line-strong);
    color: var(--ink-2);
}

.btn-ghost:hover {
    background: #f4f6f9;
    color: var(--ink);
}

.btn-warn-soft:hover {
    background: #f4f6f9;
    border-color: var(--brand);
    color: var(--brand);
}

.btn-danger-soft {
    color: var(--danger);
}

.btn-danger-soft:hover {
    background: var(--danger-soft);
    border-color: var(--danger-line);
}

.btn-danger {
    background: var(--danger);
    color: #fff;
}

.btn-danger:hover {
    background: #961c12;
}

.btn.is-loading {
    opacity: 0.65;
    pointer-events: none;
}

/* =========================================================
   FORM
========================================================= */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 18px;
}

.span-2 {
    grid-column: 1 / -1;
}

.field {
    margin-bottom: 18px;
}

.field label {
    display: block;
    margin-bottom: 6px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink-2);
}

.field input[type="text"],
.field input[type="number"],
.field input[type="date"],
.field select,
.field textarea {
    width: 100%;
    padding: 10px 13px;
    border: 1px solid var(--line-strong);
    border-radius: var(--r-control);
    background: #fff;
    color: var(--ink);
    font: 400 14.5px var(--font);
    transition: border-color 0.15s, box-shadow 0.15s;
}

.field textarea {
    min-height: 130px;
    resize: vertical;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 3px var(--brand-soft);
}

.field-hint {
    margin-top: 6px;
    font-size: 12.5px;
    color: var(--muted);
}

.affix {
    position: relative;
}

.affix > span {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-weight: 600;
    pointer-events: none;
}

.affix > input {
    padding-left: 42px !important;
}

.dropzone {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 22px;
    border: 1px dashed var(--line-strong);
    border-radius: 10px;
    background: #f8fafc;
    color: var(--muted);
    text-align: center;
    transition: border-color 0.15s, background 0.15s;
}

.dropzone:hover,
.dropzone:focus-within {
    border-color: var(--brand);
    background: var(--brand-soft);
}

.dropzone input[type="file"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.dropzone .dz-title {
    color: var(--ink-2);
    font-weight: 600;
}

.dropzone .preview {
    max-width: 100%;
    max-height: 150px;
    border-radius: 6px;
    object-fit: cover;
}

.dropzone.has-file .dz-prompt {
    display: none;
}

.dz-name {
    font-size: 13px;
    color: var(--ink-2);
    word-break: break-all;
}

/* =========================================================
   ALERT
========================================================= */
.alert {
    padding: 12px 16px;
    margin-bottom: 20px;
    border: 1px solid;
    border-left-width: 3px;
    border-radius: var(--r-control);
    font-weight: 500;
    transition: opacity 0.3s, transform 0.3s;
}

.alert.out {
    opacity: 0;
    transform: translateY(-4px);
}

.alert-success {
    background: var(--ok-soft);
    border-color: var(--ok-line);
    border-left-color: var(--ok);
    color: var(--ok);
}

.alert-error {
    background: var(--danger-soft);
    border-color: var(--danger-line);
    border-left-color: var(--danger);
    color: #8c1f15;
}

/* =========================================================
   MODAL
========================================================= */
dialog.modal {
    width: min(640px, calc(100vw - 28px));
    max-height: calc(100vh - 40px);
    padding: 0;
    border: 0;
    border-radius: 12px;
    background: var(--surface);
    color: var(--ink);
    font-family: var(--font);
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.25);
    overflow: hidden;
}

dialog.modal[open] {
    animation: pop 0.18s ease-out;
}

dialog.modal::backdrop {
    background: rgba(15, 23, 42, 0.5);
}

@keyframes pop {
    from {
        opacity: 0;
        transform: translateY(8px) scale(0.985);
    }
}

.modal form {
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - 40px);
}

.modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 18px 24px;
    border-bottom: 1px solid var(--line);
}

.modal-head h2 {
    font-size: 17px;
    font-weight: 700;
}

.modal-close {
    width: 34px;
    height: 34px;
    border: 0;
    border-radius: 8px;
    background: transparent;
    color: var(--muted);
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
}

.modal-close:hover {
    background: #f1f5f9;
    color: var(--ink);
}

.modal-body {
    padding: 22px 24px 6px;
    overflow-y: auto;
}

.modal-foot {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 14px 24px;
    border-top: 1px solid var(--line);
    background: #fafbfc;
}

dialog.modal-confirm {
    width: min(420px, calc(100vw - 28px));
}

.confirm-body {
    padding: 24px;
}

.confirm-title {
    font-size: 17px;
    font-weight: 700;
}

.confirm-text {
    margin-top: 6px;
    color: var(--muted);
}

/* =========================================================
   EMPTY STATE
========================================================= */
.empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 56px 24px;
    text-align: center;
}

.empty h3 {
    font-size: 16px;
    font-weight: 700;
}

.empty p {
    margin: 6px 0 20px;
    max-width: 360px;
    color: var(--muted);
}

/* =========================================================
   RESPONSIVE
========================================================= */
@media (max-width: 1100px) {
    .stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 960px) {
    .main {
        padding: 24px 18px 48px;
    }
}

@media (max-width: 560px) {
    .form-grid {
        grid-template-columns: 1fr;
    }

    .stats {
        grid-template-columns: 1fr;
    }

    .page-head h1 {
        font-size: 21px;
    }

    .search {
        width: 100%;
    }

    .modal-foot {
        flex-direction: column-reverse;
    }

    .modal-foot .btn {
        width: 100%;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation: none !important;
        transition: none !important;
    }
}

</style>
</head>

<body>

<?php require_once "../includes/sidebar_admin.php"; ?>

<main class="main">
<div class="wrap">

    <div class="page-head">
        <div>
            <h1>Campaign</h1>
            <p><?= count($campaigns) ?> campaign terdaftar.</p>
        </div>

        <div class="page-actions">
            <button type="button" class="btn btn-primary" data-open="tambah-campaign">
                Campaign baru
            </button>
        </div>
    </div>

    <?php tampilkan_flash($flash); ?>


    <!-- DAFTAR CAMPAIGN -->

    <section class="panel">

        <?php if (count($campaigns) > 0): ?>

            <div class="toolbar" data-table="tabel-campaign">
                <div class="segmented" data-filter-status>
                    <button type="button" class="active" data-value="">Semua</button>
                    <button type="button" data-value="aktif">Aktif</button>
                    <button type="button" data-value="ditutup">Ditutup</button>
                </div>
                <input type="search" class="search" placeholder="Cari campaign" aria-label="Cari campaign" data-search>
            </div>

            <div class="table-wrap">
                <table class="data" id="tabel-campaign">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Pendanaan</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th class="right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($campaigns as $campaign): ?>

                            <?php
                            $persen = persen($campaign["dana_terkumpul"], $campaign["target_donasi"]);
                            $aktif  = strtolower(trim($campaign["status"])) === "aktif";
                            ?>

                            <tr data-status="<?= $aktif ? "aktif" : "ditutup" ?>">
                                <td>
                                    <div class="cell-media">

                                        <?php if (!empty($campaign["gambar"])): ?>
                                            <img
                                                class="thumb"
                                                src="../uploads/campaign/<?= e($campaign["gambar"]) ?>"
                                                alt=""
                                            >
                                        <?php else: ?>
                                            <div class="thumb"><?= e(mb_strtoupper(mb_substr($campaign["judul"], 0, 1))) ?></div>
                                        <?php endif; ?>

                                        <div>
                                            <div class="cell-title"><?= e($campaign["judul"]) ?></div>
                                            <div class="cell-desc"><?= e($campaign["deskripsi"]) ?></div>
                                        </div>

                                    </div>
                                </td>

                                <td class="progress-cell">
                                    <div
                                        class="progress<?= $persen >= 100 ? ' done' : '' ?>"
                                        role="progressbar"
                                        aria-valuenow="<?= min(100, $persen) ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    >
                                        <span style="width: <?= min(100, $persen) ?>%"></span>
                                    </div>

                                    <div class="progress-meta">
                                        <span>
                                            <strong><?= rupiah($campaign["dana_terkumpul"]) ?></strong>
                                            <span class="muted">dari <?= rupiah($campaign["target_donasi"]) ?></span>
                                        </span>
                                        <strong><?= $persen > 999 ? "999+" : $persen ?>%</strong>
                                    </div>
                                </td>

                                <td>
                                    <div><?= tgl_indo($campaign["tanggal_mulai"]) ?> – <?= tgl_indo($campaign["tanggal_batas"]) ?></div>

                                    <?php if ($aktif): ?>
                                        <div class="small muted"><?= e(sisa_hari($campaign["tanggal_batas"])) ?></div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($aktif): ?>
                                        <span class="badge badge-aktif">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-tutup"><?= e(ucfirst($campaign["status"])) ?></span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="actions">

                                        <?php if ($aktif): ?>
                                            <a
                                                class="btn btn-sm btn-warn-soft"
                                                href="campaign_tutup.php?id=<?= (int) $campaign["id_campaign"] ?>"
                                                data-tone="warn"
                                                data-confirm-title="Tutup campaign ini?"
                                                data-confirm="Campaign “<?= e($campaign["judul"]) ?>” akan ditandai sebagai ditutup."
                                                data-confirm-ok="Ya, tutup"
                                            >
                                                Tutup
                                            </a>
                                        <?php endif; ?>

                                        <a
                                            class="btn btn-sm btn-danger-soft"
                                            href="kelola_campaign.php?hapus=<?= (int) $campaign["id_campaign"] ?>"
                                            data-tone="danger"
                                            data-confirm-title="Hapus campaign ini?"
                                            data-confirm="Campaign “<?= e($campaign["judul"]) ?>” akan dihapus permanen dan tidak bisa dikembalikan."
                                            data-confirm-ok="Ya, hapus"
                                        >
                                            Hapus
                                        </a>

                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="no-result" hidden>Tidak ada campaign yang cocok.</div>

        <?php else: ?>

            <div class="empty">
                <h3>Belum ada campaign</h3>
                <p>Buat campaign pertama Anda agar donatur bisa mulai berdonasi.</p>
                <button type="button" class="btn btn-primary" data-open="tambah-campaign">
                    Buat campaign
                </button>
            </div>

        <?php endif; ?>

    </section>

</div>
</main>


<!-- MODAL TAMBAH CAMPAIGN -->

<dialog class="modal" id="tambah-campaign" aria-labelledby="judul-modal-campaign">

    <form method="POST" enctype="multipart/form-data">

        <div class="modal-head">
            <h2 id="judul-modal-campaign">Campaign baru</h2>
            <button type="button" class="modal-close" data-close aria-label="Tutup">&times;</button>
        </div>

        <div class="modal-body">

            <div class="form-grid">

                <div class="field span-2">
                    <label for="judul">Judul campaign</label>
                    <input
                        type="text"
                        id="judul"
                        name="judul"
                        placeholder="Contoh: Bantu Pendidikan Anak Yatim"
                        required
                    >
                </div>

                <div class="field span-2">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        placeholder="Ceritakan tujuan dan siapa yang akan dibantu..."
                        required
                    ></textarea>
                </div>

                <div class="field span-2">
                    <label for="target_donasi">Target donasi</label>
                    <div class="affix">
                        <span>Rp</span>
                        <input
                            type="number"
                            id="target_donasi"
                            data-rupiah-input="#preview-target"
                            name="target_donasi"
                            placeholder="10000000"
                            min="1"
                            required
                        >
                    </div>
                    <div class="field-hint" id="preview-target">Masukkan nominal dalam rupiah.</div>
                </div>

                <div class="field">
                    <label for="tanggal_mulai">Tanggal mulai</label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        data-date-start
                        name="tanggal_mulai"
                        value="<?= date("Y-m-d") ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="tanggal_batas">Tanggal batas</label>
                    <input
                        type="date"
                        id="tanggal_batas"
                        data-date-end
                        name="tanggal_batas"
                        required
                    >
                </div>

                <div class="field span-2">
                    <label for="gambar">Gambar campaign</label>

                    <div class="dropzone">
                        <div class="dz-prompt">
                            <div class="dz-title">Pilih gambar</div>
                            <div class="small">JPG, PNG, atau WEBP</div>
                        </div>

                        <img class="preview" alt="Pratinjau gambar" hidden>
                        <div class="dz-name"></div>

                        <input
                            type="file"
                            id="gambar"
                            name="gambar"
                            accept=".jpg,.jpeg,.png,.webp"
                        >
                    </div>
                    <div class="field-hint">Opsional. Gambar yang jelas membantu donatur lebih percaya.</div>
                </div>

            </div>

        </div>

        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" data-close>Batal</button>
            <button type="submit" name="tambah" class="btn btn-primary">
                Simpan campaign
            </button>
        </div>

    </form>

</dialog>

<script>
(function () {
    "use strict";

    var mouseDownTarget = null;
    var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    /* -------------------------------------------------
       Dialog konfirmasi (link dengan data-confirm)
    ------------------------------------------------- */
    function getConfirmDialog() {
        var dlg = document.getElementById("confirm-dialog");
        if (dlg) return dlg;

        dlg = document.createElement("dialog");
        dlg.id = "confirm-dialog";
        dlg.className = "modal modal-confirm";
        dlg.innerHTML =
            '<div class="confirm-body">' +
                '<h2 class="confirm-title"></h2>' +
                '<p class="confirm-text"></p>' +
            '</div>' +
            '<div class="modal-foot">' +
                '<button type="button" class="btn btn-ghost" data-close>Batal</button>' +
                '<a class="btn confirm-ok" href="#"></a>' +
            '</div>';
        document.body.appendChild(dlg);
        return dlg;
    }

    function askConfirm(link) {
        var dlg = getConfirmDialog();
        var danger = link.getAttribute("data-tone") !== "warn";
        var ok = dlg.querySelector(".confirm-ok");

        dlg.querySelector(".confirm-title").textContent =
            link.getAttribute("data-confirm-title") || "Lanjutkan tindakan ini?";
        dlg.querySelector(".confirm-text").textContent =
            link.getAttribute("data-confirm") || "";
        ok.textContent = link.getAttribute("data-confirm-ok") || "Ya, lanjutkan";
        ok.className = "btn confirm-ok " + (danger ? "btn-danger" : "btn-primary");
        ok.setAttribute("href", link.getAttribute("href"));

        dlg.showModal();
    }

    /* -------------------------------------------------
       Cari + filter status pada tabel
    ------------------------------------------------- */
    function runFilter(bar) {
        var table = document.getElementById(bar.getAttribute("data-table"));
        if (!table) return;

        var input = bar.querySelector("[data-search]");
        var q = input ? input.value.toLowerCase().trim() : "";
        var active = bar.querySelector("[data-filter-status] .active");
        var status = active ? active.getAttribute("data-value") : "";
        var shown = 0;

        table.querySelectorAll("tbody tr").forEach(function (tr) {
            var okText = !q || tr.textContent.toLowerCase().indexOf(q) !== -1;
            var okStatus = !status || tr.getAttribute("data-status") === status;
            var show = okText && okStatus;
            tr.hidden = !show;
            if (show) shown++;
        });

        var none = bar.closest(".panel").querySelector(".no-result");
        if (none) none.hidden = shown !== 0;
    }

    /* -------------------------------------------------
       Angka statistik naik dari 0
    ------------------------------------------------- */
    function countUp() {
        if (reduceMotion) return;

        document.querySelectorAll("[data-count]").forEach(function (el) {
            var target = parseFloat(el.getAttribute("data-count")) || 0;
            var prefix = el.getAttribute("data-prefix") || "";
            var start = null;
            var duration = 900;

            if (target <= 0) return;

            function step(t) {
                if (start === null) start = t;
                var p = Math.min(1, (t - start) / duration);
                var eased = 1 - Math.pow(1 - p, 3);
                el.textContent = prefix + Math.round(target * eased).toLocaleString("id-ID");
                if (p < 1) requestAnimationFrame(step);
            }

            el.textContent = prefix + "0";
            requestAnimationFrame(step);
        });
    }

    /* -------------------------------------------------
       Tanggal batas tidak boleh sebelum tanggal mulai
    ------------------------------------------------- */
    function syncDates() {
        var s = document.querySelector("[data-date-start]");
        var e = document.querySelector("[data-date-end]");
        if (s && e && s.value) e.min = s.value;
    }

    /* -------------------------------------------------
       Event global
    ------------------------------------------------- */
    document.addEventListener("mousedown", function (e) {
        mouseDownTarget = e.target;
    });

    document.addEventListener("click", function (e) {
        var el = e.target;

        var opener = el.closest("[data-open]");
        if (opener) {
            var target = document.getElementById(opener.getAttribute("data-open"));
            if (target && target.showModal) target.showModal();
            return;
        }

        var closer = el.closest("[data-close]");
        if (closer) {
            var parent = closer.closest("dialog");
            if (parent) parent.close();
            return;
        }

        if (el.tagName === "DIALOG" && mouseDownTarget === el) {
            el.close();
            return;
        }

        var seg = el.closest("[data-filter-status] button");
        if (seg) {
            seg.parentNode.querySelectorAll("button").forEach(function (b) {
                b.classList.remove("active");
            });
            seg.classList.add("active");
            runFilter(seg.closest(".toolbar"));
            return;
        }

        var confirmLink = el.closest("a[data-confirm]");
        if (confirmLink) {
            e.preventDefault();
            askConfirm(confirmLink);
        }
    });

    document.addEventListener("input", function (e) {
        var t = e.target;
        if (!t.matches) return;

        if (t.matches("[data-search]")) {
            runFilter(t.closest(".toolbar"));
        }

        if (t.matches("[data-rupiah-input]")) {
            var out = document.querySelector(t.getAttribute("data-rupiah-input"));
            var v = parseFloat(t.value);
            if (out) {
                out.textContent = v > 0
                    ? "Rp " + Math.round(v).toLocaleString("id-ID")
                    : "Masukkan nominal dalam rupiah.";
            }
        }
    });

    document.addEventListener("change", function (e) {
        var input = e.target;
        if (!input.matches) return;

        if (input.matches("[data-date-start]")) syncDates();

        if (input.matches(".dropzone input[type=file]")) {
            var zone = input.closest(".dropzone");
            var img = zone.querySelector(".preview");
            var name = zone.querySelector(".dz-name");
            var file = input.files && input.files[0];

            if (!file) {
                img.hidden = true;
                img.removeAttribute("src");
                name.textContent = "";
                zone.classList.remove("has-file");
                return;
            }

            img.src = URL.createObjectURL(file);
            img.hidden = false;
            name.textContent = file.name;
            zone.classList.add("has-file");
        }
    });

    /* Cegah klik ganda. Tombol tidak di-disable agar name="tambah" tetap terkirim. */
    document.addEventListener("submit", function (e) {
        var btn = e.target.querySelector("button[type=submit]");
        if (btn) {
            setTimeout(function () {
                btn.classList.add("is-loading");
            }, 0);
        }
    });

    /* Notifikasi sukses hilang sendiri */
    document.querySelectorAll(".alert[data-autohide]").forEach(function (a) {
        setTimeout(function () {
            a.classList.add("out");
            setTimeout(function () { a.remove(); }, 300);
        }, 5000);
    });

    /* Buka modal otomatis lewat hash, mis. #tambah-campaign */
    if (location.hash.length > 1) {
        var auto = document.getElementById(location.hash.slice(1));
        if (auto && auto.tagName === "DIALOG" && auto.showModal) {
            auto.showModal();
            history.replaceState(null, "", location.pathname + location.search);
        }
    }

    syncDates();
    countUp();
})();

</script>

</body>
</html>