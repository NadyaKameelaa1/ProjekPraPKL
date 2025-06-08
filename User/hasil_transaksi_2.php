<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$id_pesanan = $_GET['id_pesanan'];

// Ambil data pembayaran
$query = "SELECT p.*, h.nama_hotel, h.kota_hotel, k.nama_kamar, k.harga_kamar, 
          py.metode_pembayaran, py.booking_status, py.id_order, py.tanggal_bayar, py.upload_bukti
          FROM pesanan p
          JOIN hotels h ON p.id_hotel = h.id_hotel
          JOIN kamar k ON p.id_kamar = k.id_kamar
          JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
          WHERE p.id_pesanan = ?";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_assoc();

if (!$transaksi) {
    die("Transaksi tidak ditemukan");
}


$email = $_SESSION['email_user'];
$user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['nama_user'] ?? 'User';

$check_in = date('d-m-Y', strtotime($transaksi['check_in']));
$check_out = date('d-m-Y', strtotime($transaksi['check_out']));
$tanggal_bayar = date('d-m-Y', strtotime($transaksi['tanggal_bayar']));
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Transaksi | Javast</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="hasil_transaksi_2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    
    <div class="navbar">
        <div class="logo">
            <img src="logo/Logo_Javast.png" alt="Logo_Javast">

        </div>
        <div class="menu">
            <a href="home.php">Beranda</a>
            <a href="tentang.php">Tentang</a>
            <a href="kontak_kami.php">Kontak Kami</a>

        </div>

        <div class="dropdown">
            <button class="dropdown-btn"> 
                <i class="fas fa-user"></i> <?= htmlspecialchars($username) ?> ▼
            </button>
            <div class="dropdown-menu">
                <a href="profil.php">Profil</a>
                <a href="booking.php">Booking</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
        
    </div>


    <br>

    <div class="steps">
        <div class="step active">Pesan</div>
        <div class="arrow">→</div>
        <div class="step active">Bayar</div>
        <div class="arrow">→</div>
        <div class="step active">Upload Bukti</div>
        <div class="arrow">→</div>
        <div class="step active">Tunggu</div>
      </div>


      <br>
    
    <div class="booking-container">
        <!-- Card 1 -->
        <div class="booking-card">
            <h3><?= htmlspecialchars($transaksi['kota_hotel']) ?></h3>
            <h4><?= htmlspecialchars($transaksi['nama_hotel']) ?></h4>
            <p><b><?= htmlspecialchars($transaksi['nama_kamar']) ?></b></p>
            <p>Rp. <?= number_format($transaksi['harga_kamar'], 0, ',', '.') ?> /Malam</p>
            <p><b>Check-out:</b> <?= $check_out ?></p>
            <p><b>Check-in:</b> <?= $check_in ?></p>
            <p><b>Jumlah:</b> Rp. <?= number_format($transaksi['total_bayar'], 0, ',', '.') ?></p>
            <p><b>Waktu:</b> <?= $tanggal_bayar ?></p>
            <p><b>Metode pembayaran:</b> <?= htmlspecialchars($transaksi['metode_pembayaran']) ?></p>
            <p><b>Bukti Foto:</b> <?= htmlspecialchars(basename($transaksi['upload_bukti'])) ?></p>
            <br>
            <span class="status pending"><?= htmlspecialchars($transaksi['booking_status']) ?></span>
            <input type="button" value="Riwayat Booking" class="buttonn-download" onclick="window.location.href='booking.php'">

        </div>
        </div>







    <br>
    <br>
    <br>
    <br>




    <footer>
        <div class="footer-container">
            <div class="footer-logo">
                
                <img src="logo/Logo_Javast.png" alt="Javast Logo">
                <p>Selamat datang di platform Javast, tempat terbaik untuk memesan hotel
                    hampir di seluruh kota di Jawa Tengah. Website ini
                    dirancang khusus untuk memudahkan Anda dalam menemukan
                    dan memesan akomodasi sesuai dengan kebutuhan, baik untuk keperluan bisnis maupun liburan.</p>
            </div>
    
            <div class="footer-links">
                <h3>Link</h3>
                <ul>
                    <li><a href="home.php">Beranda</a></li>
                    <li><a href="tentang.php">Tentang</a></li>
                    <li><a href="kontak_kami.php">Kontak Kami Us</a></li>
                </ul>
            </div>
    
            <div class="footer-social">
                <h3>Ikuti Kami</h3>
                <ul>
                    <li><a href="#"><i class="fab fa-facebook"></i> Javast</a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i> @javast.hotel</a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i> @javast.hotel</a></li>
                </ul>
            </div>
        </div>
    </footer>




</body>
</html>