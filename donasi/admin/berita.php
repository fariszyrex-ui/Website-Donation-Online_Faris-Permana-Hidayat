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
        $ikon  = $flash["tipe"] === "success" ? "check" : "alert";

        echo '<div class="alert ' . $kelas . '" role="status">'
            . icon($ikon, 18)
            . '<span>' . e($flash["pesan"]) . '</span>'
            . '</div>';
    }
}


/* =========================
   IKON (SVG inline)
========================= */

if (!function_exists("icon")) {
    function icon($nama, $ukuran = 18)
    {
        $path = [
            "grid"     => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
            "heart"    => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
            "news"     => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
            "users"    => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            "gift"     => '<polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>',
            "wallet"   => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
            "plus"     => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
            "trash"    => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
            "lock"     => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
            "logout"   => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
            "menu"     => '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
            "image"    => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>',
            "upload"   => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>',
            "x"        => '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
            "check"    => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            "alert"    => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
        ];

        $isi = $path[$nama] ?? "";

        return '<svg class="icon" width="' . (int) $ukuran . '" height="' . (int) $ukuran
            . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"'
            . ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $isi . '</svg>';
    }
}

// =========================
// HAPUS BERITA
// =========================

if (isset($_GET["hapus"])) {

    $id_berita = $_GET["hapus"];

    // Ambil nama gambar
    $query = $pdo->prepare("
        SELECT gambar
        FROM berita
        WHERE id_berita = ?
    ");

    $query->execute([$id_berita]);

    $berita = $query->fetch(PDO::FETCH_ASSOC);

    // Hapus gambar dari folder
    if ($berita && !empty($berita["gambar"])) {

        $file = "../uploads/berita/" . $berita["gambar"];

        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Hapus data dari database
    $query = $pdo->prepare("
        DELETE FROM berita
        WHERE id_berita = ?
    ");

    $query->execute([$id_berita]);

    set_flash("success", "Berita berhasil dihapus.");
    header("Location: berita.php");
    exit;
}


/* =========================
   PROSES TAMBAH BERITA
========================= */

if (isset($_POST["tambah"])) {

    $id_campaign = $_POST["id_campaign"];
    $judul       = trim($_POST["judul"]);
    $isi         = trim($_POST["isi"]);
    $id_admin    = $_SESSION["id_admin"];

    $gambar = null;
    $error  = null;


    /* =========================
       UPLOAD GAMBAR
    ========================= */

    if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] === 0) {

        $nama_file = $_FILES["gambar"]["name"];
        $tmp_file  = $_FILES["gambar"]["tmp_name"];

        $ekstensi = strtolower(
            pathinfo($nama_file, PATHINFO_EXTENSION)
        );

        $ekstensi_valid = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];

        if (!in_array($ekstensi, $ekstensi_valid)) {

            $error = "Format gambar tidak diperbolehkan. Gunakan JPG, PNG, atau WEBP.";

        } else {

            $gambar = uniqid() . "." . $ekstensi;

            $folder = "../uploads/berita/";

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            move_uploaded_file(
                $tmp_file,
                $folder . $gambar
            );
        }
    }

    if ($error) {
        set_flash("error", $error);
        header("Location: berita.php");
        exit;
    }


    /* =========================
       SIMPAN BERITA
    ========================= */

    $query = $pdo->prepare("
        INSERT INTO berita
        (
            id_campaign,
            id_admin,
            judul,
            isi,
            gambar,
            tanggal
        )
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $query->execute([
        $id_campaign,
        $id_admin,
        $judul,
        $isi,
        $gambar
    ]);

    set_flash("success", "Berita berhasil ditambahkan.");
    header("Location: berita.php");
    exit;
}


/* =========================
   AMBIL DATA CAMPAIGN
========================= */

$query = $pdo->query("
    SELECT
        id_campaign,
        judul
    FROM campaign
    ORDER BY id_campaign DESC
");

$campaign = $query->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   AMBIL DATA BERITA
========================= */

$query_berita = $pdo->query("
    SELECT
        berita.id_berita,
        berita.judul,
        berita.isi,
        berita.gambar,
        berita.tanggal,
        campaign.judul AS judul_campaign
    FROM berita
    INNER JOIN campaign
        ON berita.id_campaign = campaign.id_campaign
    ORDER BY berita.id_berita DESC
");

$data_berita = $query_berita->fetchAll(PDO::FETCH_ASSOC);

$flash = get_flash();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Berita</title>

    <style>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap");

/* =========================================================
   TOKEN
========================================================= */
:root {
    --bg: #f1f4f2;
    --surface: #ffffff;
    --ink: #132320;
    --ink-2: #3b4c48;
    --muted: #667874;
    --line: #e2e8e5;
    --line-strong: #cfd8d4;


    --brand: #1b7a63;
    --brand-strong: #14604e;
    --brand-soft: #e2f1eb;

    --warn: #a5620a;
    --warn-soft: #fdf1dc;
    --danger: #b93a2e;
    --danger-soft: #fce9e6;

    --r-panel: 14px;
    --r-control: 9px;

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
    color: var(--brand-strong);
}

:focus-visible {
    outline: 2px solid var(--brand);
    outline-offset: 2px;
}

.icon {
    flex: none;
    display: block;
}

/* =========================================================
   LAYOUT
========================================================= */
.main {
    padding: 36px 40px 64px;
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
    margin-bottom: 26px;
}

.page-head h1 {
    font-size: 26px;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.25;
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

.panel + .panel,
.stats + .panel {
    margin-top: 24px;
}

.panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 18px 22px;
    border-bottom: 1px solid var(--line);
}

.panel-head h2 {
    font-size: 16px;
    font-weight: 700;
}

.panel-head a {
    font-weight: 600;
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
    margin-bottom: 24px;
}

.stat {
    display: flex;
    gap: 14px;
    align-items: flex-start;
    padding: 22px 24px;
    background: var(--surface);
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 11px;
    display: grid;
    place-items: center;
    background: var(--brand-soft);
    color: var(--brand-strong);
}

.stat-label {
    color: var(--muted);
    font-weight: 500;
}

.stat-value {
    margin-top: 2px;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.25;
    overflow-wrap: anywhere;
}

.stat-note {
    margin-top: 2px;
    font-size: 12.5px;
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
    padding: 12px 22px;
    background: #f7f9f8;
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
    background: #fafcfb;
}

table.data th.right,
table.data td.right {
    text-align: right;
}

.cell-media {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 260px;
}

.thumb {
    width: 68px;
    height: 50px;
    flex: none;
    border-radius: 8px;
    object-fit: cover;
    background: var(--brand-soft);
    color: var(--brand);
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
    padding: 3px 10px;
    border-radius: 6px;
    background: #eef3f1;
    color: var(--ink-2);
    font-size: 13px;
    font-weight: 500;
}

/* Progress */
.progress-cell {
    min-width: 220px;
}

.progress {
    height: 8px;
    border-radius: 99px;
    background: #e5ece9;
    overflow: hidden;
}

.progress > span {
    display: block;
    height: 100%;
    border-radius: inherit;
    background: var(--brand);
    transform-origin: left center;
    animation: grow 0.9s cubic-bezier(0.2, 0.7, 0.2, 1) both;
}

.progress-meta {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 7px;
    font-size: 13px;
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
    padding: 4px 11px;
    border-radius: 99px;
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
    background: var(--brand-soft);
    color: var(--brand-strong);
}

.badge-tutup {
    background: #eceff0;
    color: #55636a;
}

/* Aksi */
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
    padding: 0 16px;
    border: 1px solid transparent;
    border-radius: var(--r-control);
    font: 600 14px var(--font);
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
}

.btn-sm {
    height: 34px;
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

.btn-ghost {
    background: #fff;
    border-color: var(--line-strong);
    color: var(--ink);
}

.btn-ghost:hover {
    background: #f4f7f6;
}

.btn-warn-soft {
    background: var(--warn-soft);
    color: var(--warn);
}

.btn-warn-soft:hover {
    background: #fae6c2;
}

.btn-danger-soft {
    background: var(--danger-soft);
    color: var(--danger);
}

.btn-danger-soft:hover {
    background: #f9d7d2;
}

.btn-danger {
    background: var(--danger);
    color: #fff;
}

.btn-danger:hover {
    background: #9e2f24;
}

.btn-warn {
    background: var(--warn);
    color: #fff;
}

.btn-warn:hover {
    background: #874f07;
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
    padding: 11px 13px;
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
    border: 1.5px dashed var(--line-strong);
    border-radius: 12px;
    background: #f8faf9;
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
    border-radius: 8px;
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
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 13px 16px;
    margin-bottom: 22px;
    border-radius: 11px;
    font-weight: 500;
}

.alert-success {
    background: var(--brand-soft);
    color: var(--brand-strong);
}

.alert-error {
    background: var(--danger-soft);
    color: #8f2a20;
}

/* =========================================================
   MODAL
========================================================= */
dialog.modal {
    width: min(640px, calc(100vw - 28px));
    max-height: calc(100vh - 40px);
    padding: 0;
    border: 0;
    border-radius: 16px;
    background: var(--surface);
    color: var(--ink);
    font-family: var(--font);
    box-shadow: 0 28px 70px rgba(13, 41, 37, 0.3);
    overflow: hidden;
}

dialog.modal[open] {
    animation: pop 0.18s ease-out;
}

dialog.modal::backdrop {
    background: rgba(13, 41, 37, 0.5);
    backdrop-filter: blur(2px);
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
    padding: 20px 24px;
    border-bottom: 1px solid var(--line);
}

.modal-head h2 {
    font-size: 18px;
    font-weight: 700;
}

.modal-close {
    display: grid;
    place-items: center;
    width: 36px;
    height: 36px;
    border: 0;
    border-radius: 9px;
    background: transparent;
    color: var(--muted);
    cursor: pointer;
}

.modal-close:hover {
    background: #f0f4f2;
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
    padding: 16px 24px;
    border-top: 1px solid var(--line);
    background: #fafcfb;
}

dialog.modal-confirm {
    width: min(430px, calc(100vw - 28px));
}

.confirm-body {
    display: flex;
    gap: 16px;
    padding: 24px;
}

.confirm-icon {
    flex: none;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: grid;
    place-items: center;
}

.modal-confirm[data-tone="danger"] .confirm-icon {
    background: var(--danger-soft);
    color: var(--danger);
}

.modal-confirm[data-tone="warn"] .confirm-icon {
    background: var(--warn-soft);
    color: var(--warn);
}

.confirm-title {
    font-size: 17px;
    font-weight: 700;
}

.confirm-text {
    margin-top: 4px;
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

.empty-icon {
    width: 52px;
    height: 52px;
    margin-bottom: 14px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: var(--brand-soft);
    color: var(--brand-strong);
}

.empty h3 {
    font-size: 16px;
    font-weight: 700;
}

.empty p {
    margin: 4px 0 18px;
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
        font-size: 22px;
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
            <h1>Berita</h1>
            <p>Kabar penyaluran dan perkembangan setiap campaign. <?= count($data_berita) ?> berita dipublikasikan.</p>
        </div>

        <div class="page-actions">
            <button type="button" class="btn btn-primary" data-open="tambah-berita">
                <?= icon("plus", 17) ?> Tulis berita
            </button>
        </div>
    </div>

    <?php tampilkan_flash($flash); ?>


    <!-- DAFTAR BERITA -->

    <section class="panel">

        <?php if (count($data_berita) > 0): ?>

            <div class="table-wrap">
                <table class="data">
                    <thead>
                        <tr>
                            <th>Berita</th>
                            <th>Campaign</th>
                            <th>Tanggal</th>
                            <th class="right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data_berita as $b): ?>

                            <tr>
                                <td>
                                    <div class="cell-media">

                                        <?php if (!empty($b["gambar"])): ?>
                                            <img
                                                class="thumb"
                                                src="../uploads/berita/<?= e($b["gambar"]) ?>"
                                                alt=""
                                            >
                                        <?php else: ?>
                                            <div class="thumb"><?= icon("image", 22) ?></div>
                                        <?php endif; ?>

                                        <div>
                                            <div class="cell-title"><?= e($b["judul"]) ?></div>
                                            <div class="cell-desc"><?= e($b["isi"]) ?></div>
                                        </div>

                                    </div>
                                </td>

                                <td>
                                    <span class="tag"><?= e($b["judul_campaign"]) ?></span>
                                </td>

                                <td class="muted">
                                    <?= tgl_indo($b["tanggal"]) ?>
                                </td>

                                <td>
                                    <div class="actions">
                                        <a
                                            class="btn btn-sm btn-danger-soft"
                                            href="berita.php?hapus=<?= (int) $b["id_berita"] ?>"
                                            data-tone="danger"
                                            data-confirm-title="Hapus berita ini?"
                                            data-confirm="Berita “<?= e($b["judul"]) ?>” akan dihapus permanen beserta gambarnya."
                                            data-confirm-ok="Ya, hapus"
                                        >
                                            <?= icon("trash", 15) ?> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>

            <div class="empty">
                <div class="empty-icon"><?= icon("news", 24) ?></div>
                <h3>Belum ada berita</h3>
                <p>Bagikan kabar terbaru agar donatur tahu bantuan mereka sudah sampai.</p>
                <button type="button" class="btn btn-primary" data-open="tambah-berita">
                    <?= icon("plus", 17) ?> Tulis berita
                </button>
            </div>

        <?php endif; ?>

    </section>

</div>
</main>


<!-- MODAL TAMBAH BERITA -->

<dialog class="modal" id="tambah-berita" aria-labelledby="judul-modal-berita">

    <form method="POST" enctype="multipart/form-data">

        <div class="modal-head">
            <h2 id="judul-modal-berita">Tulis berita</h2>
            <button type="button" class="modal-close" data-close aria-label="Tutup">
                <?= icon("x", 20) ?>
            </button>
        </div>

        <div class="modal-body">

            <div class="field">
                <label for="id_campaign">Campaign</label>

                <select id="id_campaign" name="id_campaign" required>
                    <option value="">Pilih campaign</option>

                    <?php foreach ($campaign as $c): ?>
                        <option value="<?= (int) $c["id_campaign"] ?>">
                            <?= e($c["judul"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (count($campaign) === 0): ?>
                    <div class="field-hint">
                        Belum ada campaign. <a href="kelola_campaign.php#tambah-campaign">Buat campaign</a> terlebih dahulu.
                    </div>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="judul">Judul berita</label>
                <input
                    type="text"
                    id="judul"
                    name="judul"
                    placeholder="Contoh: Bantuan Telah Disalurkan"
                    required
                >
            </div>

            <div class="field">
                <label for="isi">Isi berita</label>
                <textarea
                    id="isi"
                    name="isi"
                    placeholder="Tuliskan informasi mengenai penyaluran bantuan..."
                    required
                ></textarea>
            </div>

            <div class="field">
                <label for="gambar">Gambar dokumentasi</label>

                <div class="dropzone">
                    <div class="dz-prompt">
                        <?= icon("upload", 24) ?>
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
                <div class="field-hint">Opsional.</div>
            </div>

        </div>

        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" data-close>Batal</button>
            <button type="submit" name="tambah" class="btn btn-primary">
                Publikasikan berita
            </button>
        </div>

    </form>

</dialog>

<script>
(function () {
    "use strict";

    var body = document.body;
    var mouseDownTarget = null;

    /* -------------------------------------------------
       Dialog konfirmasi (untuk link dengan data-confirm)
    ------------------------------------------------- */
    function getConfirmDialog() {
        var dlg = document.getElementById("confirm-dialog");
        if (dlg) return dlg;

        dlg = document.createElement("dialog");
        dlg.id = "confirm-dialog";
        dlg.className = "modal modal-confirm";
        dlg.innerHTML =
            '<div class="confirm-body">' +
                '<div class="confirm-icon">' +
                    '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
                    '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>' +
                    '<line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
                '</div>' +
                '<div>' +
                    '<h2 class="confirm-title"></h2>' +
                    '<p class="confirm-text"></p>' +
                '</div>' +
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
        var tone = link.getAttribute("data-tone") === "warn" ? "warn" : "danger";
        var ok = dlg.querySelector(".confirm-ok");

        dlg.setAttribute("data-tone", tone);
        dlg.querySelector(".confirm-title").textContent =
            link.getAttribute("data-confirm-title") || "Lanjutkan tindakan ini?";
        dlg.querySelector(".confirm-text").textContent =
            link.getAttribute("data-confirm") || "";
        ok.textContent = link.getAttribute("data-confirm-ok") || "Ya, lanjutkan";
        ok.className = "btn confirm-ok " + (tone === "warn" ? "btn-warn" : "btn-danger");
        ok.setAttribute("href", link.getAttribute("href"));

        dlg.showModal();
    }

    /* -------------------------------------------------
       Klik global
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

        // Klik pada area gelap (backdrop) menutup dialog
        if (el.tagName === "DIALOG" && mouseDownTarget === el) {
            el.close();
            return;
        }

        var confirmLink = el.closest("a[data-confirm]");
        if (confirmLink) {
            e.preventDefault();
            askConfirm(confirmLink);
        }
    });

    /* -------------------------------------------------
       Preview gambar pada dropzone
    ------------------------------------------------- */
    document.addEventListener("change", function (e) {
        var input = e.target;
        if (!input.matches || !input.matches(".dropzone input[type=file]")) return;

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
    });

    /* -------------------------------------------------
       Cegah klik ganda saat form dikirim.
       (Tombol tidak di-disable agar nilai name="tambah"
       tetap ikut terkirim ke PHP.)
    ------------------------------------------------- */
    document.addEventListener("submit", function (e) {
        var btn = e.target.querySelector("button[type=submit]");
        if (btn) {
            setTimeout(function () {
                btn.classList.add("is-loading");
            }, 0);
        }
    });

    /* -------------------------------------------------
       Buka modal otomatis lewat hash, mis. #tambah-campaign
    ------------------------------------------------- */
    if (location.hash.length > 1) {
        var auto = document.getElementById(location.hash.slice(1));
        if (auto && auto.tagName === "DIALOG" && auto.showModal) {
            auto.showModal();
            history.replaceState(null, "", location.pathname + location.search);
        }
    }
})();

</script>

</body>
</html>