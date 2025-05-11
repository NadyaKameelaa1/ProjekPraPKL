<?php
session_start();
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambahPesan'])){
    // Sanitasi input - PASTIKAN NAMA FIELD SESUAI FORM
    $nama_pengirim = mysqli_real_escape_string($koneksi, $_POST['nama_pengirim'] ?? '');
    $email_pengirim = mysqli_real_escape_string($koneksi, $_POST['email_pengirim'] ?? '');
    $pesan_pengirim = mysqli_real_escape_string($koneksi, $_POST['pesan_pengirim'] ?? '');

    // Debug: Lihat data yang diterima
    error_log("Data Form: " . print_r($_POST, true));

    // Untuk pengunjung non-login
    $id_user = 0; // Nilai default untuk guest

    // Untuk user yang login
    if (isset($_SESSION['email_user'])) {
        $stmt = mysqli_prepare($koneksi, "SELECT id_user FROM users WHERE email_user = ?");
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['email_user']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id_user);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    // Insert data
    $query = "INSERT INTO kontak_kami (id_user, nama_pengirim, email_pengirim, pesan_pengirim)
              VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'isss', $id_user, $nama_pengirim, $email_pengirim, $pesan_pengirim);

    // Set session success setelah berhasil insert
    if (mysqli_stmt_execute($stmt)){
        $_SESSION['success'] = "Pesan berhasil dikirim!";
        header("Location: kontak_kami.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengirim pesan: ". mysqli_error($koneksi);
        header("Location: kontak_kami.php");
        exit;
    }
    mysqli_stmt_close($stmt);
    header("Location: kontak_kami.php");
    exit;
}