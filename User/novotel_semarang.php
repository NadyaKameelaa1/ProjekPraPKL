<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}
// Ambil id_hotel dari URL
$id_hotel = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mengambil data hotel spesifik + harga terendah
$query = mysqli_query($koneksi, "SELECT hotels.*, MIN(kamar.harga_kamar) AS harga_terendah 
    FROM hotels
    LEFT JOIN kamar ON hotels.id_hotel = kamar.id_hotel
    WHERE hotels.id_hotel = $id_hotel
    GROUP BY hotels.id_hotel");

$hotels = mysqli_fetch_assoc($query);

// Jika hotel tidak ditemukan, tampilkan pesan
if (!$hotels) {
    die("Hotel tidak ditemukan!");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santika Premiere</title>
    <link rel="stylesheet" href="santika_premiere.css">
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
            <i class="fas fa-user"></i> User_name ▼
        </button>
        <div class="dropdown-menu">
            <a href="profil.php">Profil</a>
            <a href="booking.php">Booking</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
</div>


    <div class="container-gumaya">
    <div class="hotel-card">
        
        
        <div class="hotel-info">
            <div class="description">
              <div class="header-container">
                <div class="rating-facilities-hotel">
                    <div class="rating">
                            <?php echo str_repeat("★", $hotels['bintang_hotel']); ?>
                            <i class="fa-solid fa-thumbs-up"></i>
                        </div>
                    <div class="facilities">
                            <?php 
                            // Contoh: Ambil 3 fasilitas pertama
                            $fasilitas = explode(", ", $hotels['fasilitas_hotel']);
                            for ($i = 0; $i < min(4, count($fasilitas)); $i++) {
                                echo "<span>" . trim($fasilitas[$i]) . "</span> ";
                            }
                            ?>
                            <span>Dan masih banyak lagi</span>
                        </div>  
                </div>
                <h2 class="title"><?php echo strtoupper($hotels['nama_hotel']); ?></h2>
                    <span class="hotel-type">
                        <i class="fa-solid fa-location-dot"></i><?php echo  $hotels['lokasi_hotel']; ?>
                    </span>
                
                     <div class="price-wrapper">
                        <span class="price">Rp. <?php echo number_format($hotels['harga_terendah'], 0, ',', '.'); ?></span><br>
                        <span class="per-night">1 Malam</span>
                    </div>
            </div>
        </div>

        <div class="hotel-image-container">
                <img src="/JAVAST/Admin/Gambar/Hotel/<?php echo $hotels['gambar_hotel']; ?>" alt="<?php echo $hotels['nama_hotel']; ?>" class="hotel-image">
        </div>
    </div>
    </div>
</div>


    
    <br>
    

    <!-- deskripsi singkat -->

    <div class="description-card">
      <div class="card-content">
        <p class="hotel-description">
        Menginap di Novotel Semarang adalah pilihan yang baik ketika Anda mengunjungi Sekayu. Resepsionis 24 jam tersedia untuk melayani Anda, mulai dari check-in hingga check-out, atau bantuan apa pun yang Anda butuhkan. Jika Anda menginginkan lebih, jangan ragu untuk bertanya kepada resepsionis, kami selalu siap melayani Anda. WiFi tersedia di area umum properti untuk membantu Anda tetap terhubung dengan keluarga dan teman.
          <br>
          <a href="tentang_gumaya.php" class="next-link">lihat selengkapnya &gt;&gt;</a>
      </p>
      </div>
    </div>

    <br>

    <div class="location-card">
      <div class="card-content">
        <p class="location-description"><i class="fa-solid fa-location-dot"></i>
        Jl. Pemuda No.123, Sekayu, Kec. Semarang Tengah, Kota Semarang, Jawa Tengah
        </p>
      </div>
    </div>

    <br>


    <div class="container-sidebar">
      <!-- Sidebar -->
      <div class="sidebar">
        <div class="card">
          <h5>Check-in</h5>
          <input type="text" value="Kam, 20 Feb 2025" readonly>
        </div>
        <div class="card">
          <h5>Check-out</h5>
          <input type="text" value="Kam, 21 Feb 2025" readonly>
        </div>
        <div class="card">
          <h5>FASILITAS HOTEL</h5>
          <ul>
            <li>AC</li>
            <li>Restoran</li>
            <li>Kolam Renang</li>
            <li>Lift</li>
            <li>Parkir</li>
            <li>Resepsionis 24 Jam</li>
          </ul>
        </div>
        <div class="card">
          <h5>Dewasa</h5>
          <input type="number" value="2">
        </div>
        <div class="card">
          <h5>Anak - anak</h5>
          <input type="number" value="0">
        </div>
      </div>
    
      <!-- Konten kamar -->
      <div class="kamar-content">
        <div class="kamar-card">
          <img src="gambar/Santika Premiere/Standard With 1 Queen Size Bed" alt="Kamar Hotel">
          <div class="kamar-info">
            <h4><b>Standard With 1 Queen Size Bed</b></h4>
            
            <div class="facilities">
              <span>1 queen bed</span>

              <br>
              <br>
            
             <b>Fitur</b>

             <br>

              <span>Bathub</span>
              <span>Ac</span>
              <span>Air Panas</span>
              <span>Kulkas</span>
              <span>Air Panas</span>
              <span>Smart Tv</span>
              <span>Sandal</span>
              <span>Handuk</span>
            
              <br>
              <br>

              <b>Fasilitas</b>
              <br>

              <span>Spa</span>
              <span>Bar</span>

              <br>
              <br>

              <b>Kapasitas</b>
              <br>

              <span>2 Tamu</span>
         </div>

         <div class="box-button">
            <div class="price">Rp. 650.000</div>
            <a href="detail_kmr_santika.html"><button class="btn-pilih-kamar">Pilih Kamar</button></a>
          </div>
         </div>
        </div>
    
        <div class="kamar-card">
          <img src="gambar/Novotel/Superior Dengan 1 Tempat Tidur Ukuran Queen" alt="Kamar Hotel">
          <div class="kamar-info">
            <h4><b>Superior Dengan 1 Tempat Tidur Ukuran Queen</b></h4>

            <div class="facilities">
              <span>1 queen bed</span>

              <br>
              <br>
            
             <b>Fitur</b>

             <br>

              <span>Bathub</span>
              <span>Ac</span>
              <span>Air Panas</span>
              <span>Kulkas</span>
              <span>Air Panas</span>
              <span>Smart Tv</span>
              <span>Sandal</span>
              <span>Handuk</span>
            
              <br>
              <br>

              <b>Fasilitas</b>
              <br>

              <span>Spa</span>
              <span>Bar</span>

              <br>
              <br>

              <b>Kapasitas</b>
              <br>

              <span>2 Tamu</span>
         </div>

         <div class="box-button">
            <div class="price">Rp. 700.000</div>
            <button class="btn-pilih-kamar">Pilih Kamar</button>
          </div>
          </div>
        </div>
    
        <div class="kamar-card">
          <img src="gambar/Double Superior With 1 Queen Size Bed" alt="Kamar Hotel">
          <div class="kamar-info">
            <h4><b>EDouble Superior With 1 Queen Size Bed</b></h4>
          
            <div class="facilities">
              <span>1 Bed</span>

              <br>
              <br>
            
             <b>Fitur</b>

             <br>

              <span>Bathub</span>
              <span>Ac</span>
              <span>Air Panas</span>
              <span>Kulkas</span>
              <span>Air Panas</span>
              <span>Smart Tv</span>
              <span>Sandal</span>
              <span>Handuk</span>
            
              <br>
              <br>

              <b>Fasilitas</b>
              <br>

              <span>Spa</span>
              <span>Bar</span>

              <br>
              <br>

              <b>Kapasitas</b>
              <br>

              <span>2 Tamu</span>
         </div>

         <div class="box-button">
            <div class="price">Rp. 900.000</div>
            <button class="btn-pilih-kamar">Pilih Kamar</button>
          </div>
          </div>
        </div>
      
      </div>
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
                    <li><a href="home.html">Beranda</a></li>
                    <li><a href="tentang.html">Tentang</a></li>
                    <li><a href="kontak_kami.html">Kontak Kami Us</a></li>
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
        
    </footer>


</body>
</html>