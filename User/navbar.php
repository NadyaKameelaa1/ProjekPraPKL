<?php
// Pastikan session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login menggunakan email_user (sesuai dengan kode asli kamu)
$isLoggedIn = isset($_SESSION['email_user']) && !empty($_SESSION['email_user']);

// Jika user sudah login tapi nama_user belum ada di session, ambil dari database
if ($isLoggedIn && !isset($_SESSION['nama_user'])) {
    require_once '../koneksi/koneksi.php';
    $email = $_SESSION['email_user'];
    $user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
    $user_data = mysqli_fetch_assoc($user_query);
    $_SESSION['nama_user'] = $user_data['nama_user'] ?? 'User';
}

$username = $isLoggedIn ? $_SESSION['nama_user'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="navbar.css">
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
            <?php if ($isLoggedIn): ?>
            <button class="dropdown-btn"> 
                <i class="fas fa-user"></i> <?= htmlspecialchars($username) ?> ▼
            </button>
            <div class="dropdown-menu">
                <a href="profil.php">Profil</a>
                <a href="booking.php">Booking</a>
                <a href="logout.php">Logout</a>
            </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="daftar.php" class="register-btn">Daftar</a>
                </div>
            <?php endif; ?>
        </div>
        

    </div>

</body>
</html>