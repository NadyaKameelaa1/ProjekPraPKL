<?php
session_start();
require_once '../Koneksi/koneksi.php';

$email = mysqli_real_escape_string($koneksi, $_POST['email_user']);
$password = $_POST['password_user'];

// 1. Cari user berdasarkan email
$sql = "SELECT * FROM users WHERE email_user = '$email'";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: login.php?error=Email tidak terdaftar");
    exit;
}

// 2. Ambil data user
$user = mysqli_fetch_assoc($result);

// 3. Bandingkan password (versi debug)
$input_hash = md5($password);
$db_hash = $user['password_user'];

echo "Input Hash: $input_hash<br>";
echo "DB Hash: $db_hash<br>";

if ($input_hash === $db_hash) {
    $_SESSION['email_user'] = $user['email_user'];
    $_SESSION['nama_user'] = $user['nama_user'];
    header("Location: home.php");
    exit;
} else {

    // Jika masih gagal, coba metode alternatif
    if ($password === $user['password_user']) { // Jika ternyata password tidak di-hash
        $_SESSION['email_user'] = $user['email_user'];
        $_SESSION['nama_user'] = $user['nama_user'];
        header("Location: home.php");
        exit;
    } else {
        header("Location: login.php?error=Password salah!");
        exit;
    }

}


?>