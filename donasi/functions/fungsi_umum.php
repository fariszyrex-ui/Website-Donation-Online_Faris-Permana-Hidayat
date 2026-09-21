<?php

function formatRupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal)
{
    return date('d-m-Y', strtotime($tanggal));
}
function updateCampaignExpired($pdo)
{
    // Tutup jika target sudah tercapai
    $query = $pdo->prepare("
        UPDATE campaign
        SET status = 'ditutup'
        WHERE dana_terkumpul >= target_donasi
    ");

    $query->execute();


    // Tutup jika tanggal batas SUDAH LEWAT
    // Karena tanggal_batas adalah DATE,
    // campaign masih aktif sepanjang tanggal tersebut.
    $query = $pdo->prepare("
        UPDATE campaign
        SET status = 'ditutup'
        WHERE tanggal_batas < CURDATE()
        AND dana_terkumpul < target_donasi
    ");

    $query->execute();


    // Aktifkan kembali campaign yang masih valid
    $query = $pdo->prepare("
        UPDATE campaign
        SET status = 'aktif'
        WHERE tanggal_batas >= CURDATE()
        AND dana_terkumpul < target_donasi
    ");

    $query->execute();
}

?>