<?php
session_start();
require_once '../koneksi/koneksi.php';
require_once 'C:\xampp\htdocs\JAVAST\TCPDF-main\tcpdf.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['email_user']) && !isset($_SESSION['nama_user'])) {
    $email = $_SESSION['email_user'];
    $user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
    $user_data = mysqli_fetch_assoc($user_query);
    $_SESSION['nama_user'] = $user_data['nama_user'] ?? 'User';
}

$id_pesanan = $_GET['id_pesanan'];
$bayar_di_hotel = isset($_GET['bayar_di_hotel']) ? true : false;

// Di bagian query, pastikan mengambil id_user
$query = "SELECT p.*, h.nama_hotel, h.kota_hotel, k.nama_kamar, k.harga_kamar,
          py.metode_pembayaran, py.booking_status, py.id_order, py.tanggal_bayar, py.upload_bukti,
          u.id_user
          FROM pesanan p
          JOIN hotels h ON p.id_hotel = h.id_hotel
          JOIN kamar k ON p.id_kamar = k.id_kamar
          JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
          JOIN users u ON p.id_user = u.id_user
          WHERE p.id_pesanan = ?";
          
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_assoc();

if (!$transaksi) {
    die("Transaksi tidak ditemukan");
}


$check_in = date('d-m-Y', strtotime($transaksi['check_in']));
$check_out = date('d-m-Y', strtotime($transaksi['check_out']));
$tanggal_bayar = date('d-m-Y', strtotime($transaksi['tanggal_bayar']));

// Ambil data tambahan user
$query_user = "SELECT nama_user FROM users WHERE email_user = ?";
$stmt_user = $koneksi->prepare($query_user);
$stmt_user->bind_param("s", $_SESSION['email_user']);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();

// Data untuk QR Code
// $dataPemesanan = "ID Order: ".$transaksi['id_order']."\n";
// $dataPemesanan .= "Nama: ".$user_data['nama_user']."\n";
// $dataPemesanan .= "Kota: ".$transaksi['kota_hotel']."\n";
// $dataPemesanan .= "Hotel: ".$transaksi['nama_hotel']."\n";
// $dataPemesanan .= "Kamar: ".$transaksi['nama_kamar']."\n";
// $dataPemesanan .= "Harga Kamar: Rp ".number_format($transaksi['harga_kamar'], 0, ',', '.')."\n";
// $dataPemesanan .= "Check-in: ".$check_in."\n";
// $dataPemesanan .= "Check-out: ".$check_out."\n";
// $dataPemesanan .= "Total: Rp ".number_format($transaksi['total_bayar'], 0, ',', '.')."\n"; 
// $dataPemesanan .= "Metode: ".$transaksi['metode_pembayaran']."\n";

// // Generate URL QR Code
// $qrUrl = "https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=".urlencode($dataPemesanan);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Transaksi | Javast</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="hasil_transaksi2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    
    <?php include 'navbar.php'; ?>

    <br>

    <div class="steps">
            <div class="step active">Pesan</div>
            <div class="arrow"><i class="fa-solid fa-right-long"></i></div>
            <div class="step <?= $bayar_di_hotel ? 'active' : 'active' ?>">Bayar</div>
            <div class="arrow"><i class="fa-solid fa-right-long"></i></div>
            <div class="step <?= $bayar_di_hotel ? 'bayar-di-hotel' : 'inactive' ?>">Upload Bukti</div>
            <div class="arrow"><i class="fa-solid fa-right-long"></i></div>
            <div class="step active">Tunggu</div>
        </div>

        
            <div class="success-notification">
                <h2>Pesanan dan Pembayaran berhasil!</h2>
            </div>
        
    
    <div class="booking-container">
        <!-- Card 1 -->
        <div class="booking-card">
            <h3><?= htmlspecialchars($transaksi['kota_hotel']) ?></h3>
            <!-- <p><b>Nama Pemesan:</b> htmlspecialchars($user_data['nama_user']) ?></p> -->
            <h4><?= htmlspecialchars($transaksi['nama_hotel']) ?></h4>
            <p><b><?= htmlspecialchars($transaksi['nama_kamar']) ?></b></p>
            <p>Rp. <?= number_format($transaksi['harga_kamar'], 0, ',', '.') ?> / kamar / malam</p>
            <p><b>Check-out:</b> <?= $check_out ?></p>
            <p><b>Check-in:</b> <?= $check_in ?></p>
            <p><b>Jumlah:</b> Rp. <?= number_format($transaksi['total_bayar'], 0, ',', '.') ?></p>
            <p><b>Tanggal:</b> <?= $tanggal_bayar ?></p>
            <p><b>Metode pembayaran:</b> <?= htmlspecialchars($transaksi['metode_pembayaran']) ?></p>

            <?php if (!$bayar_di_hotel): ?>
            <p><b>Bukti Foto:</b> Tidak ada bukti foto.</p>
            <?php endif;?>
            <?php
            // Generate URL untuk kuitansi PDF
            $pdfUrl = 'http://'.$_SERVER['HTTP_HOST'].'/JAVAST/User/download_kuitansi.php?id_pesanan='.$transaksi['id_pesanan'].'&view_mode=qr_access';
            
            // Generate QR Code
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=".urlencode($pdfUrl);
            ?>
            <img src="<?= $qrUrl ?>" alt="Kuitansi QR Code" width="150">
            <h6 class="keterangan">Tunjukkan QR Code resmi di atas saat anda melakukan check-in di hotel.</h6>
            <span class="status pending" value="<?= $transaksi['booking_status'] == 'Menunggu Konfirmasi Admin' ?>">
                <?= htmlspecialchars($transaksi['booking_status']) ?><br></span>
            <input type="button" value="Riwayat Booking" class="buttonn-download" onclick="window.location.href='booking.php'">

        </div>

         <!-- <div class="qr-code-section">
            <h4>QR Code Booking</h4>
            

            <div class="qr-container">
                <h4>Scan untuk Kuitansi</h4>
                <img src="= $qrUrl ?>" alt="Kuitansi QR Code" width="200">
                <p>Scan QR code untuk melihat kuitansi resmi</p>
                <small>Atau <a href="download_kuitansi.php?id_pesanan=?= $transaksi['id_pesanan'] ?>">download langsung</a></small>
            </div> -->
            <!--  if(isset($qrUrl)): ?>
                <img src=" htmlspecialchars($qrUrl) ?>" alt="QR Code Booking" width="200">
                <p>Scan untuk verifikasi booking</p>
             else: ?>
                <p class="error">QR Code tidak dapat ditampilkan</p>
            endif; ?> -->
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
                    <li><a href="kontak_kami.php">Kontak Kami</a></li>
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