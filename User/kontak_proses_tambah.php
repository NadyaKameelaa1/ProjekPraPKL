<?php
session_start();
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambahPesan'])){
    // Sanitasi input
    $nama_pengirim = mysqli_real_escape_string($koneksi, $_POST['nama_pengirim'] ?? '');
    $email_pengirim = mysqli_real_escape_string($koneksi, $_POST['email_pengirim'] ?? '');
    $pesan_pengirim = mysqli_real_escape_string($koneksi, $_POST['pesan_pengirim'] ?? '');

    // Periksa apakah user login dan id_user valid
    if (isset($_SESSION['id_user'])) {
        // Verifikasi id_user ada di tabel users/user5
        $check_user = mysqli_query($koneksi, "SELECT id_user FROM users WHERE id_user = '".$_SESSION['id_user']."'");
        if(mysqli_num_rows($check_user) > 0) {
            $query = "INSERT INTO kontak_kami (id_user, nama_pengirim, email_pengirim, pesan_pengirim)
                      VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, 'isss', $_SESSION['id_user'], $nama_pengirim, $email_pengirim, $pesan_pengirim);
        } else {
            $_SESSION['error'] = "ID User tidak valid";
            header("Location: kontak_kami.php?alert=user_tidak_valid");
            exit;
        }
    } else {
        // Jika tidak login, tapi id_user NOT NULL, beri nilai default
        $query = "INSERT INTO kontak_kami (id_user, nama_pengirim, email_pengirim, pesan_pengirim)
                  VALUES (0, ?, ?, ?)"; // Gunakan 0 atau nilai default lain
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'sss', $nama_pengirim, $email_pengirim, $pesan_pengirim);
    }

    if (mysqli_stmt_execute($stmt)){
        $_SESSION['success'] = "Pesan berhasil dikirim";
        header("Location: kontak_kami.php?success=tambah");
    } else {
        $_SESSION['error'] = "Gagal mengirim pesan: ". mysqli_error($koneksi);
        header("Location: kontak_kami.php?alert=tambah_gagal");
    }
    mysqli_stmt_close($stmt);
    exit;
}
?>