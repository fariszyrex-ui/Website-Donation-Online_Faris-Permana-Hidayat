<?php

session_start();

require_once "../config/database.php";
require_once "../auth/cek_admin.php";

if (!function_exists("e")) {
    function e($nilai)
    {
        return htmlspecialchars((string) $nilai, ENT_QUOTES, "UTF-8");
    }
}

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

/* Jam hanya ditampilkan bila datanya memang memuat waktu */
if (!function_exists("jam_indo")) {
    function jam_indo($tanggal)
    {
        if (empty($tanggal) || strlen($tanggal) <= 10) {
            return "";
        }

        $ts = strtotime($tanggal);

        return $ts ? date("H:i", $ts) . " WIB" : "";
    }
}

$query = $pdo->query("
    SELECT
        id_donatur,
        nama,
        email,
        no_hp,
        alamat
    FROM donatur
    ORDER BY id_donatur DESC
");

$donatur = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Donatur</title>

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

/* ===== tambahan halaman donatur & transaksi ===== */
.stats.cols-3 {
    grid-template-columns: repeat(3, 1fr);
}

.badge-pending {
    background: #fdf6e7;
    border-color: #f0dcae;
    color: var(--warn);
}

.badge-gagal {
    background: var(--danger-soft);
    border-color: var(--danger-line);
    color: var(--danger);
}

.avatar-sm {
    width: 34px;
    height: 34px;
    flex: none;
    border-radius: 50%;
    background: var(--brand-soft);
    color: var(--brand);
    font-size: 14px;
    font-weight: 700;
    display: grid;
    place-items: center;
}

.cell-media.compact {
    min-width: 0;
    gap: 12px;
}

table.data td.num,
table.data th.num {
    text-align: right;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
}

.stat-value.long {
    font-size: 19px;
    letter-spacing: -0.01em;
}

@media (max-width: 960px) {
    .stats.cols-3 {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 700px) {
    .stats.cols-3 {
        grid-template-columns: 1fr;
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
            <h1>Data Donatur</h1>
            <p><?= number_format(count($donatur), 0, ",", ".") ?> donatur terdaftar.</p>
        </div>
    </div>

    <section class="panel">

        <?php if (count($donatur) > 0): ?>

            <div class="toolbar" data-table="tabel-donatur">
                <input
                    type="search"
                    class="search"
                    placeholder="Cari nama, email, atau no. HP"
                    aria-label="Cari donatur"
                    data-search
                >
            </div>

            <div class="table-wrap">

                <table class="data" id="tabel-donatur">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. HP</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php $no = 1; ?>

                    <?php foreach ($donatur as $data): ?>

                        <tr>

                            <td class="muted">
                                <?= $no++ ?>
                            </td>

                            <td>
                                <div class="cell-media compact">
                                    <div class="avatar-sm"><?= e(mb_strtoupper(mb_substr($data["nama"], 0, 1))) ?></div>
                                    <div class="cell-title"><?= e($data["nama"]) ?></div>
                                </div>
                            </td>

                            <td>
                                <?= e($data["email"]) ?>
                            </td>

                            <td>
                                <?= e($data["no_hp"]) ?>
                            </td>

                            <td class="muted">
                                <?= e($data["alamat"]) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <div class="no-result" hidden>Tidak ada donatur yang cocok.</div>

        <?php else: ?>

            <div class="empty">
                <h3>Belum ada donatur</h3>
                <p>Data akan muncul di sini setelah ada yang mendaftar sebagai donatur.</p>
            </div>

        <?php endif; ?>

    </section>

</div>
</main>

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