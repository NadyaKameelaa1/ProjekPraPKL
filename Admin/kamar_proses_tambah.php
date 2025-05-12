<?php
session_start();
require_once '../koneksi/koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Ambil data dari form
    $id_hotel = mysqli_real_escape_string($koneksi, $_POST['id_hotel']);
    $nama_kamar = mysqli_real_escape_string($koneksi, $_POST['nama_kamar']);
    $tipe_kasur = mysqli_real_escape_string($koneksi, $_POST['tipe_kasur']);
    $ukuran_kamar = mysqli_real_escape_string($koneksi, $_POST['ukuran_kamar']);
    $kapasitas_kamar = (int)$_POST['kapasitas_kamar'];
    $fasilitas_kamar = mysqli_real_escape_string($koneksi, $_POST['fasilitas_kamar']);
    $harga_kamar = (int)$_POST['harga_kamar'];
    $jumlah_kamar = (int)$_POST['jumlah_kamar'];
    $jumlah_dewasa = (int)$_POST['jumlah_dewasa'];
    $jumlah_anak = (int)$_POST['jumlah_anak'];
    $deskripsi_kamar = mysqli_real_escape_string($koneksi, $_POST['deskripsi_kamar']);

    // Validasi
    $errors = [];
    if(empty($nama_kamar)) $errors[] = "Nama kamar harus diisi";
    if(empty($harga_kamar)) $errors[] = "Harga kamar harus diisi";
    if(empty($id_hotel)) $errors[] = "Hotel harus dipilih";

    if(empty($errors)) {
        // Query insert
        $query = "INSERT INTO kamar (
            id_hotel, nama_kamar, tipe_kasur, ukuran_kamar, 
            kapasitas_kamar, fasilitas_kamar, harga_kamar, jumlah_kamar, 
            jumlah_dewasa, jumlah_anak, deskripsi_kamar
        ) VALUES (
            '$id_hotel', '$nama_kamar', '$tipe_kasur', '$ukuran_kamar', 
            '$kapasitas_kamar', '$fasilitas_kamar', '$harga_kamar', '$jumlah_kamar', 
            '$jumlah_dewasa', '$jumlah_anak', '$deskripsi_kamar'
        )";

        if(mysqli_query($koneksi, $query)) {
            $_SESSION['success'] = "Kamar berhasil ditambahkan!";
            header("Location: kamar.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal menambahkan kamar: " . mysqli_error($koneksi);
            header("Location: kamar.php");
            exit;
        }
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: kamar.php");
        exit;
    }
} else {
    header("Location: kamar.php");
    exit;
}
?>