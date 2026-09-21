<?php

session_start();

require_once "../config/database.php";
require_once "../auth/cek_admin.php";

if (!isset($_GET["id"])) {
    die("Campaign tidak ditemukan.");
}

$id_campaign = $_GET["id"];

$query = $pdo->prepare("
    UPDATE campaign
    SET status = 'ditutup'
    WHERE id_campaign = ?
");

$query->execute([$id_campaign]);

header("Location: kelola_campaign.php");
exit;

?>