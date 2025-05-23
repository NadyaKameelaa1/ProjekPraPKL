<?php
session_start();
require_once '../koneksi/koneksi.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gumaya Tower</title>
    <link rel="stylesheet" href="gumaya.css">
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



    <!-- header -->

    <!-- <div class="container-gumaya">
      <div class="hotel-card">
        <div class="left-image">
          <img src="gambar/gambargumaya/gumaya.jpg" alt="Hotel">
        </div>

        
        <div class="left-info">
          <br>
          <br>
          <br>
          <br>
          <br>
          <div class="description">
            <h2>GUMAYA TOWER SEMARANG</h2>
            <span>Hotel</span>
            <div class="bintang">★★★★★</div>
            <div class="harga">RP. 1.167.076</div>
            <div class="per-malam">Per-malam</div>
          </div>
        </div>
      </div>
    </div> -->
    <div class="container-gumaya">
    <div class="hotel-card">
        
        
        <div class="hotel-info">
            <div class="description">
            <div class="header-container">
              <div class="rating-facilities-hotel">
                <div class="rating">★★★★★ <i class="fa-solid fa-thumbs-up"></i></div>
                <div class="facilities"><span>AC</span> <span>Restoran</span> <span>Kolam Renang</span> <span>Dan masih banyak lagi</span></div>
              </div>
                <h2 class="title">GUMAYA TOWER SEMARANG</h2>
                <span class="hotel-type"><i class="fa-solid fa-location-dot"></i> Hotel</span>
                
                <div class="price-container">
                <span class="price">Rp. 1.167.076</span>
                <span class="per-night">1 Malam</span>
            </div>
            </div>
        </div>

        <div class="hotel-image-container">
            <img src="gambar/gambargumaya/gumaya.jpg" alt="Hotel" class="hotel-image">
        </div>
    </div>
    </div>
</div>


    
    <br>
    

    <!-- deskripsi singkat -->

    <div class="description-card">
      <div class="card-content">
        <p class="hotel-description">
          Berlokasi di Semarang, 2 km dari Stasiun Semarang Tawang, Gumaya Tower Hotel menawarkan spa & pusat kebugaran dan pemandangan kota. Fasilitas yang tersedia di akomodasi ini adalah restoran, layanan kamar, resepsionis 24 jam, dan WiFi gratis di seluruh area akomodasi.
          <br>
          <a href="tentang_gumaya.html" class="next-link">lihat selengkapnya &gt;&gt;</a>
      </p>
      </div>
    </div>

    <br>

    <div class="location-card">
      <div class="card-content">
        <p class="location-description"><i class="fa-solid fa-location-dot"></i>
          Jl. Gajahmada No.59-61, 50134 Semarang, Indonesia
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
          <img src="gambar/new twin room gumaya.jpeg" alt="Kamar Hotel">
          <div class="kamar-info">
            <h4><b>New Deluxe Twin Room Only</b></h4>
            
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
            <a href="detail_kmr_gumaya.html"><button class="btn-pilih-kamar">Pilih Kamar</button></a>
          </div>
         </div>
        </div>
    
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

            <div class="lihat-detail">
              <a href="#">Lihat Detail >></a>
            </div>
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

            <div class="lihat-detail">
              <a href="#">Lihat Detail >></a>
            </div>
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