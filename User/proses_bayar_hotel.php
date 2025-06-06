<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$id_pesanan = $_POST['id_pesanan'];

// Generate unique ID Order
$id_order = 'JVST-' . date('Ymd') . '-' . substr(md5(uniqid(mt_rand(), true)), 0, 4);

// Simpan ke database
$query = "INSERT INTO pembayaran 
          (id_pesanan, metode_pembayaran, booking_status, id_order, tanggal_bayar)
          VALUES (?, 'Bayar di hotel', 'Belum dibayar', ?, NOW())";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("is", $id_pesanan, $id_order);

if($stmt->execute()) {
    header("Location: hasil_transaksi.php?id_pesanan=".$id_pesanan);
} else {
    die("Error menyimpan data pembayaran: " . $koneksi->error);
}
?>