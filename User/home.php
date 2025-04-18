<?php 
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="home.css">
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
            <a href="#">Hotel</a>
            <a href="tentang.php">Tentang</a>
            <a href="kontak_kami.html">Kontak Kami</a>

        </div>

        <div class="dropdown">
            <button class="dropdown-btn"> 
                <i class="fas fa-user"></i> User_name ▼
            </button>
            <div class="dropdown-menu">
                <a href="profil.php">Profil</a>
                <a href="booking.html">Booking</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <header class="header">
        <h5>Hotel Jawa Tengah</h5>
        <h2>SELAMAT DATANG</h2>
        <hr>

        <div class="search-labels">
            <div class="search-label">Kota, atau nama hotel</div>
            <div class="search-label">Tanggal Check-In & Check-Out</div>
            <div class="search-label">Tamu & Kamar</div>
          </div>
    </header>

    <div class="search-bar">
        <label><i class="fa-solid fa-location-dot"></i></label> 
              <input type="text" placeholder="Kota, atau Nama hotel">        
              
              <input type="date" placeholder="Check-in">
              <input type="date" placeholder="Check-out">   
   
              <label><i class="fa-solid fa-user-check"></i> </label> 
        <select>
            <option>2 Dewasa, 0 Anak, 1 Kamar</option>
            <option>2 Dewasa, 1 Anak, 1 Kamar</option>           
        </select>
        <button><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>

    <br>
    <br>
    <br>
    <br>
            <section class="hotel-favorit">
                <div class="hotel-title">
                 <h4>Hotel Favorit</h4>
                 <p>Berikut beberapa hotel berbitang 5 di website kami:</p>
                </div>
                
               <hr>

               <div class="container"> 
               <div class="hotel-card">
                       <img src="Gambar/GambarKota/Gumaya.jpg" alt="Gumaya Tower Hotel" class="hotel-image">
                       <div class="hotel-info">
                        <div class="hotel-name">
                            <h5>Gumaya Tower Hotel</h5>
                        </div>
                       <p class="hotel-location">Semarang</p>
                       <p class="hotel-address">Jl. Gajahmada No.59-61, 50134 Semarang, Indonesia</p>
                       <div class="hotel-rating">
                       <span>⭐⭐⭐⭐⭐</span>   
                       </div>
                       <div class="hotel-price">
                       <span>1 malam</span>
                       <span class="price">Rp. 1.167.076</span>
                   </div>
                 </div>
                </div>

                <div class="hotel-card">
                    <img src="gambar/gambarkota/PO hotel Semarang.jpeg" alt="Po Hotel Semarang" class="hotel-image">
                    <div class="hotel-info">
                        <div class="hotel-name">
                            <h5>PO hotel Semarang</h5>
                        </div>
                        <p class="hotel-location">Semarang</p>
                        <p class="hotel-address">Jl. Pemuda No 118, 50132, Semarang, Indonesia</p>
                        <div class="hotel-rating">
                            <span>⭐⭐⭐⭐⭐</span>   
                        </div>
                        <div class="hotel-price">
                            <span>1 malam</span>
                            <span class="price">Rp. 1.200.000</span>
                        </div>
                    </div>
                </div>

                <div class="hotel-card">
                    <img src="gambar/gambarkota/the royal surakrta.jpg" alt="The Royal Surakarta Heritage" class="hotel-image">
                    <div class="hotel-info">
                        <div class="hotel-name">
                            <h5>The Royal Surakarta Heritage</h5>
                        </div>
                        <p class="hotel-location">Surakarta</p>
                        <p class="hotel-address">Jl. Jl. Slamet Riyadi No.6, 57111 Solo, Indonesia</p>
                        <div class="hotel-rating">
                            <span>⭐⭐⭐⭐⭐</span>   
                        </div>
                        <div class="hotel-price">
                            <span>1 malam</span>
                            <span class="price">Rp. 500.000</span>
                        </div>
                    </div>
                </div>                
           </div>

        <div class="button-container">
            <button class="hotel-button">Lihat Hotel Lainnya >></button>
        </div>

    </section> 

    <br>
    <br>
    <br>

    <section class="kota-rekomendasi">
        <div class="hotel-title">
            <h4>Rekomendasi kota</h4>
            <p class="text">Perlu ide tujuan? kunjungi kota-kota berikut!</p>
        </div>

        <hr>

    <div class="kota">
        <div class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Semarang</h3>
            <img src="gambar/gambarkota/Semarang.jpg" alt="Semarang" class="image">
        </div>

        <div class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Jepara</h3>
            <img src="gambar/gambarkota/Jepara.jpeg" alt="Jepara" class="image">
        </div>

        <div class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Salatiga</h3>
            <img src="gambar/gambarkota/Salatiga.jpg" alt="Salatiga" class="image">
        </div>

        <div class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Jogja</h3>
            <img src="gambar/gambarkota/Jogja.jpeg" alt="Jogja" class="image">
        </div>

        <div class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Sukoharjo</h3>
            <img src="gambar/gambarkota/Sukoharjo.jpg" alt="Sukoharjo" class="image">
        </div>

        <div class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Surakarta</h3>
            <img src="gambar/gambarkota/Surakarta.jpeg" alt="Surakarta" class="image">
        </div>
    </div>

        <div class="button-container">
            <button class="kota-button">Lihat Hotel Lainnya >></button>
        </div>

    </section>

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
                    <li><a href="#">Beranda</a></li>
                    <li><a href="#">Hotel</a></li>
                    <li><a href="#">Tentang</a></li>
                    <li><a href="#">Kontak Kami Us</a></li>
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


