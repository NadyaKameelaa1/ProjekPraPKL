<?php
session_start();
require_once '../Koneksi/koneksi.php';

// Validasi dan sanitasi input
$errors = [];
$form_data = $_POST;

// Validasi wajib diisi
$required_fields = ['id_hotel', 'nama_kamar', 'tipe_kasur', 'ukuran_kamar', 'kapasitas_kamar', 'harga_kamar', 'jumlah_kamar'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Field " . str_replace('_', ' ', $field) . " harus diisi";
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $form_data;
    header("Location: kamar.php");
    exit;
}

// Proses insert data kamar
try {
    $sql = "INSERT INTO kamar (
        id_hotel, nama_kamar, tipe_kasur, ukuran_kamar, 
        kapasitas_kamar, fasilitas_kamar, harga_kamar, 
        jumlah_kamar, jumlah_dewasa, jumlah_anak, deskripsi_kamar
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param(
        $stmt, 
        "isssdsiiiss",
        $_POST['id_hotel'],
        $_POST['nama_kamar'],
        $_POST['tipe_kasur'],
        $_POST['ukuran_kamar'],
        $_POST['kapasitas_kamar'],
        $_POST['fasilitas_kamar'],
        $_POST['harga_kamar'],
        $_POST['jumlah_kamar'],
        $_POST['jumlah_dewasa'],
        $_POST['jumlah_anak'],
        $_POST['deskripsi_kamar']
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $id_kamar = mysqli_insert_id($koneksi);
        $_SESSION['success'] = "Kamar berhasil ditambahkan!";
        header("Location: kamar_detail_gambar.php?id_kamar=".$id_kamar."&nama_kamar=".urlencode($_POST['nama_kamar']));
        exit;
    } else {
        throw new Exception("Gagal menambahkan kamar: " . mysqli_error($koneksi));
    }
} catch (Exception $e) {
    $_SESSION['errors'] = [$e->getMessage()];
    $_SESSION['form_data'] = $form_data;
    header("Location: kamar.php");
    exit;
}
?>