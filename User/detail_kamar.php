<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

// Ambil parameter pencarian
$lokasi = isset($_GET['lokasi']) ? mysqli_real_escape_string($koneksi, $_GET['lokasi']) : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d');
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'));
$dewasa = isset($_GET['dewasa']) ? intval($_GET['dewasa']) : 1;
$anak = isset($_GET['anak']) ? intval($_GET['anak']) : 0;
$kamar = isset($_GET['kamar']) ? intval($_GET['kamar']) : 1;

$id_hotel = isset($_GET['id_hotel']) ? intval($_GET['id_hotel']) : 0;
$id_kamar = isset($_GET['id_kamar']) ? intval($_GET['id_kamar']) : 0;

// Query untuk mengambil detail kamar beserta semua gambar
$query = mysqli_query($koneksi, "SELECT 
    k.*, 
    h.nama_hotel,
    kg.gambarA, kg.gambarB, kg.gambarC, kg.gambarD, kg.gambarE
    FROM kamar k
    JOIN hotels h ON k.id_hotel = h.id_hotel
    LEFT JOIN kamar_gambar kg ON k.id_kamar = kg.id_kamar
    WHERE k.id_kamar = $id_kamar");

$data_kamar = mysqli_fetch_assoc($query);

if (!$data_kamar) {
    die("Kamar tidak ditemukan!");
}

$query = mysqli_query($koneksi, "SELECT hotels.*, MIN(kamar.harga_kamar) AS harga_terendah 
    FROM hotels
    LEFT JOIN kamar ON hotels.id_hotel = kamar.id_hotel
    WHERE hotels.id_hotel = $id_hotel
    GROUP BY hotels.id_hotel");

$hotels = mysqli_fetch_assoc($query);

$email = $_SESSION['email_user'];
$user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['nama_user'] ?? 'User';
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kamar <?= $hotels['nama_hotel'];?> | Javast</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="detail_kamar.css">
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


    <!-- gambar -->

    <div class="container-letak">
        <div class="left-panel">
          <h4 class="title"><?= htmlspecialchars($data_kamar['nama_kamar']) ?></h4>
        
        <!-- Radio buttons untuk slider -->
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <input type="radio" name="slider" id="img<?= $i ?>" <?= $i == 1 ? 'checked' : '' ?>>
        <?php endfor; ?>
        
        <div class="main-image">
            <?php 
            $gambar_fields = ['gambarA', 'gambarB', 'gambarC', 'gambarD', 'gambarE'];
            foreach ($gambar_fields as $index => $field): 
                if (!empty($data_kamar[$field])): 
                    $img_num = $index + 1;
                    $img_path = '/JAVAST/Admin/Gambar/Kamar/'.$data_kamar[$field];
            ?>
                <img src="<?= $img_path ?>" class="img img<?= $img_num ?>" alt="Gambar Kamar <?= $img_num ?>">
            <?php endif; endforeach; ?>
        </div>
        
        <div class="thumbnails">
            <?php 
            foreach ($gambar_fields as $index => $field): 
                if (!empty($data_kamar[$field])): 
                    $img_num = $index + 1;
                    $img_path = '/JAVAST/Admin/Gambar/Kamar/'.$data_kamar[$field];
            ?>
                <label for="img<?= $img_num ?>">
                    <img src="<?= $img_path ?>" alt="Thumbnail <?= $img_num ?>">
                </label>
            <?php endif; endforeach; ?>
        </div>
        </div>
    
        <div class="right-panel">
          <h3><b>Informasi Kamar</b></h3>
          <ul>
        <li><i class="fa-solid fa-bed"></i> <?= htmlspecialchars($data_kamar['tipe_kasur']) ?></li>
        <li><i class="fa-solid fa-ruler"></i> <?= htmlspecialchars($data_kamar['ukuran_kamar']) ?> m²</li>
        <li><i class="fa-solid fa-user"></i>  <?= ($data_kamar['jumlah_dewasa'] + $data_kamar['jumlah_anak']) ?> orang</li>
    </ul>
          <hr>
          <h4><b>Fasilitas Kamar</b></h4>
          <ul class="fasilitas">
            <?php
            $fitur = explode(',', $data_kamar['fasilitas_kamar']);
            foreach ($fitur as $item): 
            $item = trim($item);
            if (!empty($item)):
            ?>
            <li><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($item) ?></li>
        <?php endif; endforeach; ?>
          </ul>
          <hr>
          <h4><b>Deskripsi Kamar</b></h4>
          <p><?= htmlspecialchars($data_kamar['deskripsi_kamar']) ?></p>
          <hr>
          <p>mulai dari</p>
          <p class="harga"><b>Rp. <?= number_format($data_kamar['harga_kamar'], 0, ',', '.') ?> </b><span>/ kamar / malam</span></p>
            <a href="pesan.php?id_hotel=<?= $data_kamar['id_hotel'] ?>&id_kamar=<?= $data_kamar['id_kamar'] ?>&check_in=<?= htmlspecialchars($check_in) ?>&check_out=<?= htmlspecialchars($check_out) ?>&dewasa=<?= $dewasa ?>&anak=<?= $anak ?>&kamar=<?= $kamar ?>" 
       class="btn-pilih">
                <button class="pesan">Pesan</button>
            </a>
        </div>
      </div>


<!-- footer -->

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