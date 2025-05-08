<?php
session_start();
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambahPesan'])){
    // Sanitasi input (pastikan nama field sesuai form)
    $nama_pengirim = mysqli_real_escape_string($koneksi, $_POST['name_pengirim'] ?? '');
    $email_pengirim = mysqli_real_escape_string($koneksi, $_POST['email_pengirim'] ?? '');
    $pesan_pengirim = mysqli_real_escape_string($koneksi, $_POST['pesan_pengirim'] ?? '');

    // Periksa apakah user login dan dapatkan id_user
    if (isset($_SESSION['email_user'])){
        // Dapatkan id_user berdasarkan email
        $check_user = mysqli_query($koneksi, "SELECT id_user FROM users WHERE email_user = '".$_SESSION['email_user']."'");
        if(mysqli_num_rows($check_user) > 0){
            $user_data = mysqli_fetch_assoc($check_user);
            $id_user = $user_data['id_user'];
            
            $query = "INSERT INTO kontak_kami (id_user, nama_pengirim, email_pengirim, pesan_pengirim)
                      VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, 'isss', $id_user, $nama_pengirim, $email_pengirim, $pesan_pengirim);
        } else {
            $_SESSION['error'] = "User tidak ditemukan";
            header("Location: kontak_kami.php?alert=user_tidak_valid");
            exit;
        }
    } else {
        // Untuk pengunjung non-login, gunakan nilai default untuk id_user
        $query = "INSERT INTO kontak_kami (id_user, nama_pengirim, email_pengirim, pesan_pengirim)
                  VALUES (0, ?, ?, ?)"; // Anggap 0 adalah guest user
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'sss', $nama_pengirim, $email_pengirim, $pesan_pengirim);
    }

    if (mysqli_stmt_execute($stmt)){
        $_SESSION["success"] = "Pesan berhasil dikirim";
        header("Location: kontak_kami.php?success=tambah");
    } else {
        $_SESSION["error"] = "Gagal mengirim pesan: ". mysqli_error($koneksi);
        header("Location: kontak_kami.php?alert=tambah_gagal");
    }
    mysqli_stmt_close($stmt);
    exit;
}