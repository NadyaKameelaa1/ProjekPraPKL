<?php
session_start();
require_once '../koneksi/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

// Ambil data user
$email = $_SESSION['email_user'];
$sql = "SELECT * FROM users WHERE email_user = '$email'";
$result = mysqli_query($koneksi, $sql);
$user = mysqli_fetch_assoc($result);

// Simpan nilai awal
$initial_values = [
    'nama_user' => $user['name_user'],
    'no_telp' => $user['no_telp'],
    'alamat_user' => $user['alamat_user']
];
?>
    

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil | Javast</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="profil.css">


</head>
<body>

    <div class="navbar">
        <div class="logo">
            <img src="logo/Logo_Javast.png" alt="Logo_Javast">

        </div>
        <div class="menu">
            <a href="index.php">Beranda</a>
            <a href="#">Hotel</a>
            <a href="tentang.html">Tentang</a>
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
        <img src="ornamen/ornament-top.png" class="ornament ornament-top">
        <hr>
        <br>
        <h5>Javast</h5>
        <h2>Profil</h2>
        <img src="ornamen/ornament-bottom.png" class="ornament ornament-top">
    </header>

    <div class="profile-container">
        <div class="card">
            <h2>Informasi Dasar</h2>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            
            <form action="proses_profil.php" method="post">
            <div class="form-group">
                <label><i class="fa fa-user"></i>Nama</label>
                <input type="text" name="nama_user" value="<?= htmlspecialchars($user['nama_user']) ?>">
            </div>
            <div class="form-group">
                <label><i class="fa fa-envelope"></i>Email</label>
                <input type="email" value="<?= htmlspecialchars($user['email_user']) ?>" readonly>
            </div>
            <div class="form-group">
                <label><i class="fa fa-phone"></i>No. Telp</label>
                <input type="text" name="no_telp" value="<?= htmlspecialchars($user['no_telp']) ?>">
            </div>
            <div class="form-group">
                <label><i class="fa fa-map-marker-alt"></i>Alamat</label>
                <input type="text" name="alamat_user" value="<?= htmlspecialchars($user['alamat_user']) ?>">
            </div>
            <div class="form-group">
                <label><i class="fa fa-lock"></i>Password</label>
                <input type="password" placeholder="Password" readonly>
                <small class="text-muted">Password tidak dapat dilihat demi keamanan</small>
            </div>
            <br>
            <br>
            <input type="hidden" name="original_nama" value="<?= $initial_values['nama_user'] ?>">
            <input type="hidden" name="original_telp" value="<?= $initial_values['no_telp'] ?>">
            <input type="hidden" name="original_alamat" value="<?= $initial_values['alamat_user'] ?>">
            <input type="submit" class="button-simpan" value="Simpan Perubahan">
            </form>
        </div>

        <div class="card">
            <h2>Ubah Password</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

            <form action="proses_ubah_password.php" method="post">
            <div class="form-group">
                <label><i class="fa fa-key"></i>Password Lama</label>
                <input type="password" name="old_password" required>
            </div>
            <div class="form-group">
                <label><i class="fa fa-key"></i>Password Baru</label>
                <input type="password" name="new_password" required>
            </div>

            <div class="form-group">
                <label><i class="fa fa-key"></i>Konfirmasi Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <br>
            <br>
           <input type="submit" class="button-simpan" value="Simpan Perubahan">
            </form>
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    
    if (newPass !== confirmPass) {
        e.preventDefault();
        alert('Konfirmasi password tidak sesuai!');
    }
});
    </script>

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
</body>
</html>