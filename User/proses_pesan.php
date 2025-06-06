<?php 
session_start();
require_once '../koneksi/koneksi.php';

// Cek login
if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

// Dapatkan id_user
$email = $_SESSION['email_user'];
$query_user = "SELECT id_user FROM users WHERE email_user = ?";
$stmt = $koneksi->prepare($query_user);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Error: User not found");
}

$user = $result->fetch_assoc();
$id_user = $user['id_user'];

// Ambil data dari form
// $id_hotel = intval($_POST['id_hotel']);
// $id_kamar = intval($_POST['id_kamar']);
// $check_in = isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d');
// $check_out = isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'));
// $dewasa = intval($_POST['dewasa']);
// $anak = intval($_POST['anak']);
// $total_bayar = intval($_POST['total_bayar']);
// $jumlah_kamar = intval($_POST['kamar']);
// $jumlah_hari = isset($_GET['jumlah_hari']) ? intval($_GET['jumlah_hari']) : 0;
// $total_bayar = isset($_GET['total_bayar']) ? intval($_GET['total_bayar']) : 0;

$id_hotel = intval($_POST['id_hotel']);
$id_kamar = intval($_POST['id_kamar']);
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$dewasa = intval($_POST['dewasa']);
$anak = intval($_POST['anak']);
$jumlah_kamar = intval($_POST['kamar']);
$total_bayar = intval($_POST['total_bayar']);
$jumlah_hari = intval($_POST['jumlah_hari']);

// Validasi kamar tersedia
$check_kamar = "SELECT 1 FROM kamar WHERE id_kamar = ? AND id_hotel = ?";
$stmt_check = $koneksi->prepare($check_kamar);
$stmt_check->bind_param("ii", $id_kamar, $id_hotel);
$stmt_check->execute();

if ($stmt_check->get_result()->num_rows == 0) {
    die("Kamar tidak valid atau tidak tersedia");
}

// Hitung jumlah hari
// $tanggal1 = new DateTime($check_in);
// $tanggal2 = new DateTime($check_out);
// $jumlah_hari = $tanggal2->diff($tanggal1)->days;
// Query INSERT yang benar
$query = "INSERT INTO pesanan (id_hotel, id_user, id_kamar, total_bayar, jumlah_hari, check_in, check_out, jumlah_dewasa, jumlah_anak, jumlah_kamar, tanggal_pesan)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("iiiisssiii", 
    $id_hotel,      // i
    $id_user,       // i
    $id_kamar,      // i
    $total_bayar,   // i
    $jumlah_hari,   // i
    $check_in,      // s
    $check_out,     // s
    $dewasa,        // i
    $anak,          // i
    $jumlah_kamar   // i
);

if ($stmt->execute()) {
    $id_pesanan = $koneksi->insert_id;
    
    // Debug data
    error_log("Pesanan created - ID: $id_pesanan, Kamar: $id_kamar, Hotel: $id_hotel");
    
    header("Location: pembayaran.php?id_hotel=$id_hotel&id_kamar=$id_kamar&id_pesanan=$id_pesanan&check_in=$check_in&check_out=$check_out&dewasa=$dewasa&anak=$anak&kamar=$kamar&jumlah_hari=$jumlah_hari&total_bayar=$total_bayar");
    
    exit();
} else {
    die("Error: ". $koneksi->error);
}
?>

