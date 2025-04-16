<?php
session_start();
require_once '../Koneksi/koneksi.php';

// Cek apakah ada perubahan
$is_changed = false;

// Bandingkan nilai baru dengan nilai awal
if ($_POST['nama_user'] != $_POST['original_nama'] ||
    $_POST['no_telp'] != $_POST['original_telp'] ||
    $_POST['alamat_user'] != $_POST['original_alamat'] ||
    !empty($_POST['password_user'])) {
    $is_changed = true;
}

// Jika tidak ada perubahan
if (!$is_changed) {
    $_SESSION['error'] = "Tidak ada perubahan data";
    header("Location: profil.php");
    exit;
}

// Jika ada perubahan, proses update
$email = $_SESSION['email_user'];
$nama = mysqli_real_escape_string($koneksi, $_POST['nama_user']);
$no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
$alamat = mysqli_real_escape_string($koneksi, $_POST['alamat_user']);

// Query update dasar
$sql = "UPDATE users SET 
        nama_user = '$nama',
        no_telp = '$no_telp',
        alamat_user = '$alamat' 
        WHERE email_user = '$email'";

// Jika password diisi
if (!empty($_POST['password_user'])) {
    $password = md5($_POST['password_user']); // Gunakan password_hash() di produksi
    $sql = "UPDATE users SET 
            nama_user = '$nama',
            no_telp = '$no_telp',
            alamat_user = '$alamat',
            password_user = '$password' 
            WHERE email_user = '$email'";
}

mysqli_query($koneksi, $sql);
$_SESSION['success'] = "Profil berhasil diperbarui";
header("Location: profil.php");
exit;
?>