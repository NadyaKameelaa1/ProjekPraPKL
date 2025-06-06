<?php
session_start();
require_once '../koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

// Ambil data dari form
$metode = $_POST['metode_pembayaran'];
$id_pesanan = $_POST['id_pesanan'];

// Generate unique ID Order
$id_order = 'JVST-' . date('Ymd') . '-' . substr(md5(uniqid(mt_rand(), true)), 0, 4);

// Proses upload file
$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/JAVAST/Admin/Gambar/Bukti/";
$file_extension = pathinfo($_FILES["bukti_pembayaran"]["name"], PATHINFO_EXTENSION);
$new_filename = 'BUKTI-' . $id_order . '.' . $file_extension;
$target_file = $target_dir . $new_filename;

// Validasi file
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);
    if($check === false) {
        die("File bukan gambar valid");
    }
}

// Cek ukuran file
if ($_FILES["bukti_pembayaran"]['size'] > 2000000) {
    die("Ukuran file terlalu besar (max 2MB)");
}

// Ekstensi file yang diizinkan
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
if(!in_array(strtolower($file_extension), $allowed_extensions)) {
    die("Hanya menerima file JPG, JPEG, PNG & GIF");
}

// Buat folder jika belum ada
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Debug path
echo "Mencoba menyimpan ke: " . $target_file;
echo "<br>Folder writable: " . (is_writable($target_dir)) ? 'Ya' : 'Tidak';

// Upload file
if (move_uploaded_file($_FILES["bukti_pembayaran"]['tmp_name'], $target_file)) {
    // Simpan ke database
    $query = "INSERT INTO pembayaran
              (id_pesanan, metode_pembayaran, booking_status, upload_bukti, id_order, tanggal_bayar)
              VALUES (?, ?, 'Menunggu Konfirmasi Admin', ?, ?, NOW())";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("isss", $id_pesanan, $metode, $new_filename, $id_order);

    if($stmt->execute()) {
        header("Location: hasil_transaksi.php?id_pesanan=".$id_pesanan);
    } else {
        // Hapus file yang sudah terupload jika gagal simpan ke database
        unlink($target_file);
        die("Error menyimpan data pembayaran: " . $koneksi->error);
    }
} else {
    die("Gagal upload file. Pastikan folder upload ada dan memiliki permission yang cukup.");
}
?>