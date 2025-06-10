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
$id_kamar = isset($_GET['id_kamar']) ? intval($_GET['id_kamar']) : 0;

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



$hotel_descriptions = [
    2042 => "Berlokasi strategis tepat di tepi Pantai Bandengan yang populer, D’SEASON Premiere menjadi hotel bintang 3 terbaik untuk Anda yang ingin berlibur dengan menikmati pemandangan  laut yang begitu memesona berbalut suasana Jawa yang kental. Akomodasi ini menjadi opsi paling tepat bagi penikmat pantai maupun penyuka alam yang ingin sejenak melepaskan diri dari penatnya kesibukan. D'SEASON Premiere menawarkan kemudahan akses ke berbagai tempat wisata menarik di Jepara, seperti Pantai Bandengan, Pantai Kartini, Benteng Portugis, dan Museum Kartini. Akomodasi ini juga sangat mudah dijangkau baik dengan kendaraan pribadi maupun moda transportasi umum. Tak hanya itu, fasilitas yang ditawarkan juga memadai, memenuhi kebutuhan tamu yang menginap untuk kebutuhan bisnis maupun berlibur. Tipe kamarnya juga cukup variatif, memberikan pilihan yang lebih beragam untuk tamu sehingga bisa mendapatkan kamar yang sesuai dengan kebutuhan. D'SEASON Premiere Jepara memanjakan tamu yang menginap dengan menyediakan fasilitas yang lengkap, baik di dalam maupun di luar kamar. Beberapa fasilitas di kamar antara lain AC, televisi layar datar dengan saluran premium, air mineral gratis, kulkas, meja, serta mesin pembuat kopi dan teh. Beberapa tipe kamar juga dilengkapi dengan kamar mandi pribadi dan bathtub serta balkon yang menawarkan pemandangan pantai yang luas dan sangat menawan. Sementar itu, di luar kamar, D’SEASON Premiere menawarkan fasilitas penunjang berupa kolam renang, area bermain anak, pusat kebugaran, spa, restoran, dan bar. Bagi tamu yang memiliki kepentingan bisnis atau acara khusus, tersedia pula area fungsional dengan alat penunjang yang lengkap dan modern.Tak ketinggalan, guna memenuhi kebutuhan para tamu selama menginap, seperti penatu dan penitipan bagasi, Hotel D’SEASON Premiere juga didukung dengan layanan resepsionis 24 jam. Hotel D'SEASON Premiere beralamat di Jalan Pariwisata No.9, Bandengan, Jepara, Jawa Tengah. Aksesnya cukup mudah untuk dijangkau para tamu yang menggunakan kendaraan pribadi maupun moda transportasi umum. Tamu yang menggunakan kendaraan pribadi dapat mengakses Jalan Jepara-Bangsri, lalu berbelok ke Jalan Raya Tirta Samudra hingga sampai ke akomodasi. Tamu bisa memarkir kendaraan di area yang tersedia dengan jaminan keamanan 24 jam. Sementara bagi tamu yang menggunakan bus, Terminal Jepara akan menjadi destinasi pemberhentian terakhir. Lalu, tamu bisa melanjutkan perjalanan dengan moda transportasi umum lainnya.",
    
    2027 => "Berlokasi di Semarang, 2 km dari Stasiun Semarang Tawang, Gumaya Tower Hotel menawarkan spa & pusat kebugaran dan pemandangan kota. Fasilitas yang tersedia di akomodasi ini adalah restoran, layanan kamar, resepsionis 24 jam, dan WiFi gratis di seluruh area akomodasi.Brown Canyon lokasinya sejauh 16 km, dan Tugu Muda berjarak 2 km dari hotel. Hotel menyediakan kamar ber-AC dengan meja kerja, mesin kopi, kulkas, brankas, TV layar datar, dan kamar mandi pribadi dengan bidet. Di Gumaya Tower Hotel, setiap kamar memiliki sprei dan handuk. Sarapan hariannya menawarkan pilihan prasmanan, ala Amerika, atau Asia. Gumaya Tower Hotel menawarkan akomodasi bintang 5 dengan sauna dan kolam renang outdoor sepanjang tahun.Gumaya Tower Hotel Semarang terletak di Jalan Gajahmada Nomor 59-61, Kembangsari, Kecamatan Semarang Tengah, Kota Semarang, Jawa Tengah. Berada di pusat kota, hotel ini mudah diakses dengan transportasi umum maupun kendaraan pribadi. Selain itu, para tamu dapat dengan mudah mengakses berbagai destinasi utama di Semarang. Hotel ini menyediakan layanan sewa mobil dan pusat layanan taksi untuk memudahkan Anda menjelajahi kota. Berbagai tipe kamar mewah juga tersedia di Gumaya Tower Hotel Semarang.",
    
    2032 => "Ibis Styles Semarang adalah pilihan akomodasi yang tepat bagi Anda yang ingin menginap di Kota Semarang. Lokasinya yang berada di pusat kota, membuat hotel ini mudah dijangkau dan memberikan kemudahan akses ke berbagai tempat penting di Kota Semarang. Cocok bagi Anda yang ingin berlibur maupun mengadakan perjalanan bisnis di Kota Semarang. Dengan lokasi yang strategis di pusat kota Semarang, Ibis Styles Semarang sangat cocok untuk akomodasi berlibur maupun perjalanan bisnis. Ada banyak tempat-tempat menarik dan penting di Semarang yang lokasinya tidak jauh dari hotel. Sehingga Anda dapat dengan mudah mengakses berbagai tempat wisata, pusat perbelanjaan, restoran, dan fasilitas umum lainnya. Selain itu hotel ini juga memberikan kenyamanan maksimal karena setiap kamarnya sudah dilengkapi dengan fasilitas modern yang akan memenuhi kebutuhan Anda selama menginap.",
];

    function getHotelDescription($hotel_id, $hotel_name = '') {
    global $hotel_descriptions;
    
    if (isset($hotel_descriptions[$hotel_id])) {
        return $hotel_descriptions[$hotel_id];
    }
    
    // Fallback if no description exists
    return "Selamat datang di " . htmlspecialchars($hotel_name) . ". Hotel ini menawarkan akomodasi yang nyaman dengan fasilitas dan layanan yang sangat baik untuk masa menginap Anda. Nikmati keramahtamahan terbaik dan fasilitas modern selama kunjungan Anda.";
}

$current_hotel_description = getHotelDescription($id_hotel, $hotels['nama_hotel']);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $hotels['nama_hotel'];?> | Javast</title>
    <link rel="stylesheet" href="hotel2.css">
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
      <!-- <div class="container-header">
      <a href="hasil_pencarian.php?
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
    </div> -->
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
        <h4>Deskripsi Hotel</h4>
        <p class="hotel-description">
            <?php echo $current_hotel_description; ?>
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
  <?php while ($data_kamar = mysqli_fetch_assoc($query_kamar)): ?>
    <?php
    // Cari gambar pertama yang tersedia
    $thumbnail = "";
    $gambar_fields = ['gambarA', 'gambarB', 'gambarC', 'gambarD', 'gambarE'];
    
    foreach ($gambar_fields as $field) {
        if (!empty($data_kamar[$field])) {
            $thumbnail_path = '/JAVAST/Admin/Gambar/Kamar/'.$data_kamar[$field];
            $full_path = $_SERVER['DOCUMENT_ROOT'].$thumbnail_path;

            if (file_exists($full_path)) {
                $thumbnail = $thumbnail_path;
                break;
            }
        }
    }
    ?>
        <div class="kamar-card">
          <?php if ($thumbnail): ?>
            <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($data_kamar['nama_kamar']) ?>">
        <?php else: ?>
            <img src="gambar/default-room.jpg" alt="Kamar Default">
        <?php endif; ?>
          <div class="kamar-info">
             <h4><?= htmlspecialchars($data_kamar['nama_kamar']) ?></h4>
             
            
            <div class="facilities">
              <span><?= htmlspecialchars($data_kamar['tipe_kasur']) ?></span>
              <br>
              <br>
            
             <b>Fasilitas</b>

             <br>

              <?php
              $fasilitas = explode(',', $data_kamar['fasilitas_kamar']);
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

              <span><?= $data_kamar['jumlah_dewasa'] + $data_kamar['jumlah_anak'] ?> Tamu</span>

              <br> 
              <br>
              
              <b>Ketersediaan kamar</b>
              <br>
              <span><?= $data_kamar['jumlah_kamar'] ?> Kamar </span>
          </div>

          <div class="box-button">
              <div class="price">Rp. <?= number_format($data_kamar['harga_kamar'], 0, ',', '.') ?></div><br>
              <div class="btn-pilih-kamar">
              <a href="#" onclick="showNotification()">Pilih Kamar</a>
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
        
    </footer>

<script>
    function showNotification() {
    alert("Isi search bar terlebih dahulu!");
    // Optional: If you still want to redirect after the alert
    window.location.href = "home.php";
}
</script>

</body>
</html>