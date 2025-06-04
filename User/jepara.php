<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$query = mysqli_query($koneksi, "SELECT 
    h.*,
    MIN(k.harga_kamar) AS harga_terendah
    FROM hotels h
    LEFT JOIN kamar k ON h.id_hotel = k.id_hotel
    WHERE h.kota_hotel LIKE '%Jepara%'
    GROUP BY h.id_hotel
    ORDER BY h.bintang_hotel DESC, harga_terendah ASC");

$hotels = [];
while ($row = mysqli_fetch_assoc($query)) {
    $hotels[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semarang | Javast</title>
    <link rel="stylesheet" href="semarang.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
        
        <h5>Javast</h5>
        <h2>Semarang</h2>
        <hr>
    </header>

    <br>
   
        
            <div class="container-hotel">
        
        <?php if (count($hotels) > 0): ?>
            <div class="hotel-list">
                <?php foreach ($hotels as $hotel): ?>
                    <div class="hotel-card">
                        <div class="hotel-image">
                            <img src="/JAVAST/Admin/Gambar/Hotel/<?= htmlspecialchars($hotel['gambar_hotel']) ?>" 
                                 alt="<?= htmlspecialchars($hotel['nama_hotel']) ?>">
                        </div>
                        
                        <div class="hotel-info">
                            <h2><?= htmlspecialchars($hotel['nama_hotel']) ?></h2>
                            <div class="rating">
                                Bintang <?= $hotel['bintang_hotel'] ?>
                                <span class="stars"><?= str_repeat('★', $hotel['bintang_hotel']) ?></span>
                            </div>
                            <div class="location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($hotel['alamat_hotel']) ?>
                            </div>
                            <div class="facilities">
                                <?php 
                                $fitur = array_slice(explode(',', $hotel['fasilitas_hotel']), 0, 6);
                                foreach ($fitur as $item): 
                                    $item = trim($item);
                                    if (!empty($item)):
                                ?>
                                    <span><?= htmlspecialchars($item) ?></span>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="hotel-booking">
                            <div class="price">1 malam <br><strong>Rp <?= number_format($hotel['harga_terendah'], 0, ',', '.') ?></strong></div>
                            <div class="note">Di luar pajak & biaya</div>
                            <div>
                                <a href="detail_hotel.php?id_hotel=<?= $hotel['id_hotel']; ?>">
                                    <button class="button">Pilih Kamar</button>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-results">Tidak ada hotel yang ditemukan di Semarang.</p>
        <?php endif; ?>
    </div>

        </div>
     

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

