<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email_user'];
$sql = "SELECT * FROM users WHERE email_user = '$email'";
$result = mysqli_query($koneksi, $sql);
$user = mysqli_fetch_assoc($result);

$user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['nama_user'] ?? 'User';


// Ambil data pembayaran
$query = "SELECT p.*, h.nama_hotel, h.kota_hotel, k.nama_kamar, k.harga_kamar,
          py.metode_pembayaran, py.booking_status, py.id_order, py.tanggal_bayar, py.upload_bukti
          FROM pesanan p
          JOIN hotels h ON p.id_hotel = h.id_hotel
          JOIN kamar k ON p.id_kamar = k.id_kamar
          JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
          JOIN users u ON p.id_user = u.id_user
          WHERE u.email_user = '$email'
          ORDER BY p.tanggal_pesan DESC";

$result = mysqli_query($koneksi, $query);

// Ambil data tambahan user
$query_user = "SELECT nama_user FROM users WHERE email_user = ?";
$stmt_user = $koneksi->prepare($query_user);
$stmt_user->bind_param("s", $_SESSION['email_user']);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking | Javast</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="booking.css">


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

    <header class="header">
        <img src="ornamen/ornament-top.png" class="ornament ornament-top">
        <h5>Javast</h5>
        <h2>Booking</h2>
        <hr>
        
        <p>Halaman ini menunjukkan riwayat bookingmu.</p>
        <img src="ornamen/ornament-bottom.png" class="ornament ornament-bottom">
    </header>

    <br>
    <br>

    <div class="booking-container">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($transaksi = mysqli_fetch_assoc($result)): ?>
                <?php
                // Format tanggal
                $check_in = date('d-m-Y', strtotime($transaksi['check_in']));
                $check_out = date('d-m-Y', strtotime($transaksi['check_out']));
                $tanggal_bayar = date('d-m-Y', strtotime($transaksi['tanggal_bayar']));
                ?>
                
                <div class="booking-card">
                    <h3><?= htmlspecialchars($transaksi['kota_hotel']) ?></h3>
                    <h4><?= htmlspecialchars($transaksi['nama_hotel']) ?></h4>
                    <p><b><?= htmlspecialchars($transaksi['nama_kamar']) ?></b></p>
                    <p>Rp. <?= number_format($transaksi['harga_kamar'], 0, ',', '.') ?> /Malam</p>
                    <p><b>Check-out:</b> <?= $check_out ?></p>
                    <p><b>Check-in:</b> <?= $check_in ?></p>
                    <p><b>Total Bayar:</b> Rp. <?= number_format($transaksi['total_bayar'], 0, ',', '.') ?></p>
                    <p><b>Waktu:</b> <?= $tanggal_bayar ?></p>
                    <p><b>Metode pembayaran:</b> <?= htmlspecialchars($transaksi['metode_pembayaran']) ?></p>
                    
                    <?php if ($transaksi['metode_pembayaran'] != 'Bayar di hotel'): ?>
                        <p><b>Bukti Foto:</b> <?= htmlspecialchars(basename($transaksi['upload_bukti'])) ?></p>
                    <?php else:?>
                        <p><b>Bukti Foto:</b> Tidak ada bukti foto.</p>
                    <?php endif; ?>
                    
                    <p><b>ID Order:</b> <?= htmlspecialchars($transaksi['id_order']) ?></p>
                    
                    <span class="status <?php
                        if ($transaksi['booking_status'] == 'Menunggu Konfirmasi Admin') {
                            echo 'pending';
                        } elseif ($transaksi['booking_status'] == 'Dibatalkan') {
                            echo 'cancelled';
                        } elseif ($transaksi['booking_status'] == 'Dibayar') {
                            echo 'paid';
                        } elseif ($transaksi['booking_status'] == 'Belum dibayar') {
                            echo 'pending';
                        } elseif ($transaksi['booking_status'] == 'Selesai') {
                            echo 'selesai';
                        } else {
                            echo 'pending'; // default fallback
                        }
                    ?>">
                        <?= htmlspecialchars($transaksi['booking_status']) ?>
                    </span>
                    
                    <button class="buttonn-download" onclick="window.location.href='download_kuitansi.php?id_pesanan=<?= $transaksi['id_pesanan'] ?>'">
                        Download Kuitansi
                    </button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada riwayat booking.</p>
        <?php endif; ?>
    </div>


<br><br><br><br>



    
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