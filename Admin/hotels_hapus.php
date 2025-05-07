<?php
session_start();
require_once '../koneksi/koneksi.php';

// Pastikan ID hotel diterima
if (!isset($_GET['id_hotel']) || !is_numeric($_GET['id_hotel'])) {
    $_SESSION['error'] = "ID hotel tidak valid";
    header("Location: hotels.php");
    exit;
}

$id_hotel = (int)$_GET['id_hotel'];

// 1. Ambil nama file gambar sebelum menghapus data
$sql_select = "SELECT gambar_hotel FROM hotels WHERE id_hotel = ?";
$stmt_select = mysqli_prepare($koneksi, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $id_hotel);
mysqli_stmt_execute($stmt_select);
mysqli_stmt_bind_result($stmt_select, $gambar_hotel);
mysqli_stmt_fetch($stmt_select);

if (empty($gambar_hotel)) {
    $_SESSION['error'] = "Data hotel tidak ditemukan";
    header("Location: hotels.php");
    exit;
}

// 2. Hapus file gambar dari server jika bukan default.jpg
$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/JAVAST/Admin/Gambar/Hotel/";

if ($gambar_hotel !== 'default.jpg' && file_exists($target_dir . $gambar_hotel)) {
    if (!unlink($target_dir . $gambar_hotel)) {
        $_SESSION['warning'] = "Data hotel dihapus tetapi gagal menghapus gambar";
    }
}

// Tutup statement select sebelum membuat statement baru
mysqli_stmt_close($stmt_select);

// 3. Hapus data dari database
$sql_delete = "DELETE FROM hotels WHERE id_hotel = ?";
$stmt_delete = mysqli_prepare($koneksi, $sql_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $id_hotel);
$query = mysqli_stmt_execute($stmt_delete);

if ($query) {
    $_SESSION['success'] = "Data hotel berhasil dihapus";
} else {
    $_SESSION['error'] = "Gagal menghapus data hotel: " . mysqli_error($koneksi);
}

// Tutup statement delete
mysqli_stmt_close($stmt_delete);

header("Location: hotels.php");
exit;
?>