<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id_pesanan'])) {
    $id_pesanan = $_GET['id_pesanan'];
    
    // Update status pembayaran menjadi 'Dibatalkan'
    $query = "UPDATE pembayaran SET 
              booking_status = 'Dibatalkan',
              tanggal_bayar = NOW()
              WHERE id_pesanan = ?";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
    mysqli_stmt_execute($stmt);
    
    // Redirect ke booking.php setelah berhasil update
    header("Location: booking.php?status_batal=success");
    exit;
} else {
    header("Location: booking_terbaru.php?status_batal=failed");
    exit;
}
?>