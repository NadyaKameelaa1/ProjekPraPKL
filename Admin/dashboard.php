<?php

session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

// total booking hari ini
$query_today = "SELECT COUNT(*) as total FROM pesanan WHERE DATE(tanggal_pesan) = CURDATE()";
$result_today = mysqli_query($koneksi, $query_today);
$today_booking = mysqli_fetch_assoc($result_today)['total'];

// pesan kontak yang belum dibaca
$query_unread = "SELECT COUNT(*) as total FROM kontak_kami WHERE status_dibaca = 0";
$result_unread = mysqli_query($koneksi, $query_unread);
$unread_contact = mysqli_fetch_assoc($result_unread)['total'];

// total semua booking
$query_total = "SELECT 
                COUNT(*) as total_count,
                SUM(CASE WHEN py.booking_status = 'Dibayar' THEN p.total_bayar ELSE 0 END) as total_amount
                FROM pesanan p
                JOIN pembayaran py ON p.id_pesanan = py.id_pesanan";
$result_total = mysqli_query($koneksi, $query_total);
$total_stats = mysqli_fetch_assoc($result_total);

// booking aktif (status booking: dibayar)
$query_active = "SELECT 
                COUNT(*) as active_count,
                SUM(p.total_bayar) as active_amount
                FROM pesanan p
                JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
                WHERE py.booking_status = 'Dibayar'";
$result_active = mysqli_query($koneksi, $query_active);
$active_stats = mysqli_fetch_assoc($result_active);

// booking dibatalkan
$query_cancelled = "SELECT 
                   COUNT(*) as cancelled_count,
                   SUM(p.total_bayar) as cancelled_amount
                   FROM pesanan p
                   JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
                   WHERE py.booking_status = 'Dibatalkan'";
$result_cancelled = mysqli_query($koneksi, $query_cancelled);
$cancelled_stats = mysqli_fetch_assoc($result_cancelled);

// perlu verifikasi (status: belum dibayar/menunggu konfirmasi)
$query_pending = "SELECT COUNT(*) as pending_count
                 FROM pembayaran
                 WHERE booking_status IN ('Belum dibayar', 'Menunggu Konfirmasi Admin')";
$result_pending = mysqli_query($koneksi, $query_pending);
$pending_count = mysqli_fetch_assoc($result_pending)['pending_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Javast - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="dashboard.css">

</head>
<body>
    
    <div class="navbar">
        <div class="logo">
            <img src="logo/Logo_Javast.png" alt="Logo_Javast">

        </div>
        <div>
            <button class="button" onclick="window.location.href='login.php'"> 
                <i class="fas fa-user"></i> Log Out
            </button>
        </div>

    </div>
    
<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-header">
            <br><br><br><br><br>
            <h1><b>ADMIN PANEL</b></h1>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item active" onclick="window.location.href='dashboard.php'">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </div>
            <div class="menu-item" onclick="window.location.href='users.php'">
                <i class="fas fa-users"></i> Users
            </div>
            <div class="menu-item" onclick="window.location.href='hotels.php'">
                <i class="fas fa-hotel"></i> Hotel
            </div>
            <div class="menu-item" onclick="window.location.href='kamar.php'">
                <i class="fas fa-bed"></i> Kamar
            </div>
            <div class="menu-item" onclick="window.location.href='kontak.php'">
                <i class="fas fa-envelope"></i> Kontak Kami
            </div>
            <div class="menu-item" onclick="window.location.href='booking.php'">
                <i class="fas fa-calendar-check"></i> Booking <i class="fa-solid fa-caret-up"></i>
            </div>
        </div>
    </div>
</div>




<div class="main-content">
    <br>
    <br>
    <br>
    <br>
    <div class="header">
        <h1>DASHBOARD</h1>
    </div>

    <div class="stats-grid">
        <div class="stat-card booking-card">
          <h3 class="booking">BOOKING</h3>
          <div class="booking-value"><?= $today_booking ?></div>
        </div>

        <div class="stat-card kontak-card">
          <h3 class="kontak">KONTAK KAMI</h3>
          <div class="kontak-value"><?= $unread_contact ?></div>
        </div>
      </div>
  
      <div class="header">
        <h1>Analisis Booking</h1>
    </div>

      <div class="analysis-grid">
        <div class="stat-card total-card">
          <h3 class="total">TOTAL BOOKING</h3>
          <div class="total-value"><?= $total_stats['total_count'] ?></div>
          <div class="total-amount">RP. <?= number_format($total_stats['total_amount'], 0, ',', '.') ?></div>
        </div>
        <div class="stat-card">
          <h3 class="bookingAktif">BOOKING AKTIF</h3>
          <div class="bookingAktif-value"><?= $active_stats['active_count'] ?></div>
          <div class="bookingAktif-amount">RP. <?= number_format($active_stats['active_amount'], 0, ',', '.') ?></div>
        </div>
        <div class="stat-card">
          <h3 class="bookingBatal">BOOKING DIBATALKAN</h3>
          <div class="bookingBatal-value"><?= $cancelled_stats['cancelled_count'] ?></div>
          <div class="bookingBatal-amount">RP. Rp. <?= number_format($cancelled_stats['cancelled_amount'], 0, ',', '.') ?></div>
        </div>
      </div>
  
      <div class="header">
        <h1>Booking Terbaru</h1>
    </div>

      <div class="analysis-card">
        <h3 class="bookingTerbaru">PERLU VERIFIKASI</h3>
        <div class="bookingTerbaru-value"><?= $pending_count ?></div>
        <a href="booking_terbaru.php" class="detail-link">DETAIL >></a>
      </div>
    </div>




</body>
</html>