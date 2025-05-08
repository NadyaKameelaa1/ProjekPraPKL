<?php
session_start();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami | Javast</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="kontak_kami.css">

</head>
<body>

    <div class="navbar">
        <div class="logo">
            <img src="logo/Logo_Javast.png" alt="Logo_Javast">

        </div>
        <div class="menu">
            <a href="home.php">Beranda</a>
            <a href="#">Hotel</a>
            <a href="tentang.html">Tentang</a>
            <a href="kontak_kami.php">Kontak Kami</a>

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
        <img src="ornamen/ornament-top.png" class="ornament ornament-top">
        <h5>Javast</h5>
        <h2>Kontak Kami</h2>
        <hr>
        <img src="ornamen/ornament-bottom.png" class="ornament ornament-bottom">
    </header>

<br>
<br>

    <div class="container">
        <div class="card contact-info">
            <h3>Kontak Kami</h3>
            <p><i class="fas fa-phone"></i> +62 873 8372 3748</p>
            <p><i class="fas fa-phone"></i> +62 838 2846 1957</p>
            <h3>Email</h3>
            <p><i class="fas fa-envelope"></i> nadyakameela162@gmail.com</p>
            <p><i class="fas fa-envelope"></i> arinihusnas@gmail.com</p>
            <h3>Ikuti Kami</h3>
            <div class="social-icons">
                <i class="fab fa-facebook"></i>
                <i class="fab fa-instagram"></i>
                <i class="fab fa-twitter"></i>
            </div>
        </div>

        <div class="card contact-form">
            <h3>Kirimkan kami pesan!</h3>
            <form action="kontak_proses_tambah.php" method="post">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Nama</label>
                    <input type="text" name="nama_pengirim" placeholder="Tulis nama...">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email_pengirim" placeholder="Tulis email...">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-comment"></i> Pesan</label>
                    <textarea name="pesan_pengirim" placeholder="Tulis pesan..."></textarea>
                   
                </div>
                <input type="submit" class="button" value="Kirim" name="tambahPesan">
            </form>
            
        </div>
    </div>

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
                        <li><a href="#">Beranda</a></li>
                        <li><a href="#">Hotel</a></li>
                        <li><a href="tentang.html">Tentang</a></li>
                        <li><a href="kontak_kami.html">Kontak Kami</a></li>
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
        

    </div>