<?php

/*
 * SIDEBAR ADMIN
 * Simpan sebagai: includes/sidebar_admin.php
 *
 * Jika nama file halaman Anda berbeda, cukup ubah kolom pertama
 * pada daftar $menu_admin di bawah ini.
 */

$halaman_aktif = basename($_SERVER["SCRIPT_NAME"]);

$menu_admin = [
    ["dashboard.php",        "Dashboard"],
    ["kelola_campaign.php",  "Kelola Campaign"],
    ["transaksi.php",        "Transaksi Donasi"],
    ["donatur.php",          "Data Donatur"],
    ["berita.php",           "Kelola Berita"],
];

$link_logout = "../auth/logout.php";

$nama_admin = $_SESSION["nama"] ?? "Admin";
$inisial    = mb_strtoupper(mb_substr($nama_admin, 0, 1));

?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>

    /* Ruang untuk sidebar. Dipasang di <html> agar tidak bentrok dengan gaya body tiap halaman. */
    html {
        padding-left: 240px;
    }

    .sb,
    .sb *,
    .sb-topbar,
    .sb-topbar * {
        box-sizing: border-box;
        font-family: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
    }

    .sb {
        position: fixed;
        inset: 0 auto 0 0;
        width: 240px;
        display: flex;
        flex-direction: column;
        padding: 22px 14px 16px;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        z-index: 40;
        transition: transform 0.22s ease;
    }

    .sb-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 2px 10px 24px;
    }

    .sb-mark {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #1e3a5f;
        color: #ffffff;
        font-size: 16px;
        font-weight: 700;
        display: grid;
        place-items: center;
    }

    .sb-name {
        font-size: 15px;
        font-weight: 700;
        line-height: 1.25;
        color: #0f172a;
    }

    .sb-sub {
        font-size: 12.5px;
        color: #64748b;
    }

    .sb-nav {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .sb-nav a {
        position: relative;
        display: block;
        padding: 10px 12px 10px 16px;
        border-radius: 8px;
        color: #475569;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }

    .sb-nav a:hover {
        background: #f4f6f9;
        color: #0f172a;
    }

    .sb-nav a.active {
        background: #eef2f7;
        color: #1e3a5f;
        font-weight: 600;
    }

    .sb-nav a.active::before {
        content: "";
        position: absolute;
        left: 0;
        top: 9px;
        bottom: 9px;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: #1e3a5f;
    }

    .sb-nav a:focus-visible,
    .sb-logout:focus-visible,
    .sb-topbar button:focus-visible {
        outline: 2px solid #1e3a5f;
        outline-offset: 2px;
    }

    .sb-foot {
        margin-top: auto;
        padding: 16px 6px 0;
        border-top: 1px solid #e5e7eb;
    }

    .sb-user {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 14px;
    }

    .sb-avatar {
        width: 34px;
        height: 34px;
        flex: none;
        border-radius: 50%;
        background: #eef2f7;
        color: #1e3a5f;
        font-size: 14px;
        font-weight: 700;
        display: grid;
        place-items: center;
    }

    .sb-user-name {
        font-size: 13.5px;
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sb-user-role {
        font-size: 12px;
        color: #64748b;
    }

    .sb-user > div:last-child {
        min-width: 0;
    }

    .sb-logout {
        display: block;
        height: 36px;
        line-height: 34px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        color: #334155;
        font-size: 13.5px;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }

    .sb-logout:hover {
        background: #fdf1ef;
        border-color: #f1c3bd;
        color: #b42318;
    }

    /* Bar atas untuk layar kecil */
    .sb-topbar {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 56px;
        padding: 0 12px;
        align-items: center;
        gap: 10px;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        z-index: 30;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .sb-topbar button {
        height: 38px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #ffffff;
        color: #334155;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
    }

    .sb-scrim {
        display: none;
    }

    @media (max-width: 960px) {

        html {
            padding-left: 0;
            padding-top: 56px;
        }

        .sb {
            transform: translateX(-100%);
        }

        body.sb-open .sb {
            transform: none;
            box-shadow: 0 0 40px rgba(15, 23, 42, 0.25);
        }

        .sb-topbar {
            display: flex;
        }

        body.sb-open .sb-scrim {
            display: block;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            z-index: 35;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sb {
            transition: none;
        }
    }

</style>


<header class="sb-topbar">
    <button type="button" id="sb-toggle" aria-controls="sb" aria-expanded="false">Menu</button>
    <span>Panel Admin</span>
</header>

<div class="sb-scrim" id="sb-scrim"></div>

<aside class="sb" id="sb">

    <div class="sb-brand">
        <div class="sb-mark">D</div>
        <div>
            <div class="sb-name">Donasi</div>
            <div class="sb-sub">Panel Admin</div>
        </div>
    </div>

    <nav class="sb-nav" aria-label="Menu utama">
        <?php foreach ($menu_admin as $item): ?>
            <a
                href="<?= htmlspecialchars($item[0]) ?>"
                class="<?= $halaman_aktif === $item[0] ? "active" : "" ?>"
                <?= $halaman_aktif === $item[0] ? 'aria-current="page"' : "" ?>
            ><?= htmlspecialchars($item[1]) ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="sb-foot">
        <div class="sb-user">
            <div class="sb-avatar"><?= htmlspecialchars($inisial) ?></div>
            <div>
                <div class="sb-user-name"><?= htmlspecialchars($nama_admin) ?></div>
                <div class="sb-user-role">Administrator</div>
            </div>
        </div>

        <a class="sb-logout" href="<?= htmlspecialchars($link_logout) ?>">Keluar</a>
    </div>

</aside>

<script>
    (function () {
        var body = document.body;
        var toggle = document.getElementById("sb-toggle");
        var scrim = document.getElementById("sb-scrim");

        function setOpen(open) {
            body.classList.toggle("sb-open", open);
            toggle.setAttribute("aria-expanded", open ? "true" : "false");
        }

        toggle.addEventListener("click", function () {
            setOpen(!body.classList.contains("sb-open"));
        });

        scrim.addEventListener("click", function () {
            setOpen(false);
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") setOpen(false);
        });
    })();
</script>