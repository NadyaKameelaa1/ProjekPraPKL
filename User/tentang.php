<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email_user'];
$user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['nama_user'] ?? 'User';

// Get count of distinct cities
$kota_query = mysqli_query($koneksi, "SELECT COUNT(DISTINCT kota_hotel) as total_kota FROM hotels");
$kota_data = mysqli_fetch_assoc($kota_query);
$total_kota = $kota_data['total_kota'];

// Get count of hotels
$hotel_query = mysqli_query($koneksi, "SELECT COUNT(*) as total_hotel FROM hotels");
$hotel_data = mysqli_fetch_assoc($hotel_query);
$total_hotel = $hotel_data['total_hotel'];

// Get count of users
$kostumer_query = mysqli_query($koneksi, "SELECT COUNT(*) as total_kostumer FROM users");
$kostumer_data = mysqli_fetch_assoc($kostumer_query);
$total_kostumer = $kostumer_data['total_kostumer'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang | Javast</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="tentang.css">
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

    <header class="header">
        <img src="ornamen/ornament-top.png" class="ornament ornament-top">
        <h5>Javast</h5>
        <h2>Tentang</h2>
        <hr>
        
        <p>Selamat datang di platform Javast, tempat terbaik untuk memesan hotel <br> hampir di seluruh kota di Jawa Tengah.
            Website ini dirancang khusus untuk memudahkan Anda dalam menemukan <br>dan memesan akomodasi sesuai dengan kebutuhan, baik
            untuk keperluan bisnis maupun liburan.</p>
        <img src="ornamen/ornament-bottom.png" class="ornament ornament-bottom">
    </header>


    <section class="team-section">
        <h3 class="team-title">Team</h3>
        <hr>
        <div class="team-container">
            <div class="team-member">
                <img src="Gambar/Tentang/arini.jpg" alt="Arini Husna Sabila">
                <h4>Arini Husna Sabila</h4>
                <p>Front-End Developer</p>
            </div>
            <div class="team-member">
                <img src="Gambar/Tentang/nadya.jpg" alt="Nadya Kameela">
                <h4>Nadya Kameela</h4>
                <p>Back-End Developer</p>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="stat-box">
            <div class="stat-header"></div>
            <img src="Gambar/Tentang/kota.png" alt="Kota">
            <p><b><?= $total_kota ?>+ Kota</b></p>
        </div>
        <div class="stat-box">
            <div class="stat-header"></div>
            <img src="Gambar/Tentang/hotel.png" alt="Hotel">
            <p><b><?= $total_hotel ?>+ Hotel</b></p>
        </div>
        <div class="stat-box">
            <div class="stat-header"></div>
            <img src="Gambar/Tentang/users.png" alt="Kostumer">
            <p><b><?= $total_kostumer ?>+ Kostumer</b></p>
        </div>
    </section>

    <br>
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
