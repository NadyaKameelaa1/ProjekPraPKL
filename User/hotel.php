<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}


$email = $_SESSION['email_user'];
$user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['nama_user'] ?? 'User';

// Ambil parameter pencarian
$lokasi = isset($_GET['lokasi']) ? mysqli_real_escape_string($koneksi, $_GET['lokasi']) : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d');
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'));
$dewasa = isset($_GET['dewasa']) ? intval($_GET['dewasa']) : 1;
$anak = isset($_GET['anak']) ? intval($_GET['anak']) : 0;
$kamar = isset($_GET['kamar']) ? intval($_GET['kamar']) : 1;

$id_hotel = isset($_GET['id_hotel']) ? intval($_GET['id_hotel']) : 0;

// $deskripsi_hotel = [
//     1 => "Hotel Xamar adalah hotel bintang 5 dengan fasilitas mewah...",
//     2 => "Hotel Superior menawarkan kamar nyaman dengan pemandangan kota...",
//     3 => "Hotel Budget dengan harga terjangkau dan fasilitas lengkap...",
//     // Tambahkan deskripsi untuk hotel lainnya
// ];
// Query untuk hotel
$query = mysqli_query($koneksi, "SELECT hotels.*, MIN(kamar.harga_kamar) AS harga_terendah 
    FROM hotels
    LEFT JOIN kamar ON hotels.id_hotel = kamar.id_hotel
    WHERE hotels.id_hotel = $id_hotel
    GROUP BY hotels.id_hotel");

$hotels = mysqli_fetch_assoc($query);

$query_fasilitas_hotel = mysqli_query($koneksi, "SELECT fasilitas_hotel FROM hotels WHERE id_hotel = '$id_hotel'");

// Query untuk kamar dengan gambar
$query_kamar = mysqli_query($koneksi, "SELECT
    k.*,
    kg.gambarA, kg.gambarB, kg.gambarC, kg.gambarD, kg.gambarE
    FROM kamar k
    LEFT JOIN kamar_gambar kg ON k.id_kamar = kg.id_kamar
    WHERE k.id_hotel = $id_hotel");
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $hotels['nama_hotel'];?> | Javast</title>
    <link rel="stylesheet" href="hotel.css">
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

    <!-- <div class="container-header">
    
</div> -->

    <div class="container-gumaya">
      
    <div class="hotel-card">
      <div class="container-header">
      <a href="hasil_pencarian.php?<?php
        echo http_build_query([
            'lokasi' => $_GET['lokasi'] ?? '',
            'check_in' => $_GET['check_in'] ?? '',
            'check_out' => $_GET['check_out'] ?? '',
            'dewasa' => $_GET['dewasa'] ?? 1,
            'anak' => $_GET['anak'] ?? 0,
            'kamar' => $_GET['kamar'] ?? 1
        ]);
    ?>" class="back-button">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
    </div>
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
                        <i class="fa-solid fa-location-dot"></i> <?php echo  $hotels['lokasi_hotel']; ?>
                    </span>

                    <span class="hotel-alamat">
                        <?php echo  $hotels['alamat_hotel']; ?>
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
          <?php echo $deskripsi_hotel[$id_hotel] ?? "Deskripsi belum tersedia."; ?>
      </p>
      </div>
    </div>

    <br>

    


    <div class="container-sidebar">
      <!-- Sidebar -->
      <div class="sidebar">
        <div class="card">
          <h5>Check-in</h5>
          <input type="text" value="<?= $check_in ?>" readonly>
        </div>
        <div class="card">
          <h5>Check-out</h5>
          <input type="text" value="<?= $check_out ?>" readonly>
        </div>
        <div class="card">
          <h5>Fasilitas Hotel</h5>
          <ul>
            
              <?php while ($f_hotel = mysqli_fetch_assoc($query_fasilitas_hotel)): ?>
              <?php
              $fasilitas = explode(',', $f_hotel['fasilitas_hotel']);
              foreach ($fasilitas as $item) {
                  $item = trim($item);
                  if (!empty($item)) {
                      echo '<li>'.htmlspecialchars($item).'</li> ';
                  }
              }
              ?>
              <?php endwhile ?>
          </ul>
        </div>
        <div class="card">
          <h5>Dewasa</h5>
          <input type="number" value="<?= $_GET['dewasa'] ?? '1' ?>" readonly>
        </div>
        <div class="card">
          <h5>Anak - anak</h5>
          <input type="number" value="<?= $_GET['anak'] ?? '0' ?>" readonly>
        </div>
      </div>
    
      <!-- Konten kamar -->
<div class="kamar-content">
  <?php while ($kamar = mysqli_fetch_assoc($query_kamar)): ?>
    <?php
    // Cari gambar pertama yang tersedia
    $thumbnail = "";
    $gambar_fields = ['gambarA', 'gambarB', 'gambarC', 'gambarD', 'gambarE'];
    
    foreach ($gambar_fields as $field) {
        if (!empty($kamar[$field])) {
            $thumbnail_path = '/JAVAST/Admin/Gambar/Kamar/'.$kamar[$field];
            $full_path = $_SERVER['DOCUMENT_ROOT'].$thumbnail_path;
            
            // Debugging - tampilkan path yang dicoba
            // echo "Checking: ".$full_path."<br>";
            
            if (file_exists($full_path)) {
                $thumbnail = $thumbnail_path;
                break;
            }
        }
    }
    ?>
        <div class="kamar-card">
          <?php if ($thumbnail): ?>
            <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($kamar['nama_kamar']) ?>">
        <?php else: ?>
            <img src="gambar/default-room.jpg" alt="Kamar Default">
        <?php endif; ?>
          <div class="kamar-info">
             <h4><?= htmlspecialchars($kamar['nama_kamar']) ?></h4>
            
            <div class="facilities">
              <span><?= htmlspecialchars($kamar['tipe_kasur']) ?></span>
              <br>
              <br>
            
             <b>Fasilitas</b>

             <br>

              <?php
              $fasilitas = explode(',', $kamar['fasilitas_kamar']);
              $counter = 0; // Penghitung untuk menentukan <br>

              echo '<div class="facilities-container">'; // Container utama

              foreach ($fasilitas as $item) {
                  $item = trim($item);
                  if (!empty($item)) {
                      echo '<span class="facility-item">' . htmlspecialchars($item) . '</span>';
                      
                      $counter++;
                      // Tambah <br> setelah setiap 3 fasilitas
                      if ($counter % 3 == 0) {
                          echo '<br class="facility-break">';
                      }
                  }
              }

              echo '</div>';
              ?>

              <b>Kapasitas</b>
              <br>

              <span><?= $kamar['jumlah_dewasa'] + $kamar['jumlah_anak'] ?> Tamu</span>
          </div>

          <div class="box-button">
              <div class="price">Rp. <?= number_format($kamar['harga_kamar'], 0, ',', '.') ?></div><br>
              <div class="btn-pilih-kamar">
              <a href="detail_kamar.php?id_hotel=<?= $kamar['id_hotel'] ?>&id_kamar=<?= $kamar['id_kamar'] ?>&check_in=<?= htmlspecialchars($check_in) ?>&check_out=<?= htmlspecialchars($check_out) ?>&dewasa=<?= (int)$dewasa ?>&anak=<?= (int)$anak ?>&kamar=<?= (int)$kamar ?>" 
       class="btn-pilih">Pilih Kamar</a>
              </div>
              <br>
              
          </div>
         </div>
        </div>
         <?php endwhile; ?>
        
<!-- 
        <div class="kamar-card">
          <img src="gambar/new deluxe king bed.jpeg" alt="Kamar Hotel">
          <div class="kamar-info">
            <h4><b>New Deluxe King Bed</b></h4>

            <div class="facilities">
              <span>1 Ranjang Twin</span>

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
            <div class="price">Rp. 1.200.000</div>
            <button class="btn-pilih-kamar">Pilih Kamar</button>

          </div>
          </div>
        </div>
    
        <div class="kamar-card">
          <img src="gambar/GRand deluxe twin.jpeg" alt="Kamar Hotel">
          <div class="kamar-info">
            <h4><b>Grand Deluxe Twin</b></h4>
          
            <div class="facilities">
              <span>1 Ranjang Twin</span>

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
            <div class="price">Rp. 1.100.000</div>
            <button class="btn-pilih-kamar">Pilih Kamar</button>

            <div class="lihat-detail">
              <a href="#">Lihat Detail >></a>
            </div>
          </div>

          </div>
        </div>

        <div class="kamar-card">
          <img src="gambar/tower club.jpeg" alt="Kamar Hotel">
          <div class="kamar-info">
            <h4><b>Tower club</b></h4>
           
            <div class="facilities">
              <span>1 Ranjang Twin</span>

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
            <div class="price">Rp. 1.100.000</div>
            <button class="btn-pilih-kamar">Pilih Kamar</button>

          </div>
          </div>
        </div> -->
      
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
                    <li><a href="#">Hotel</a></li>
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