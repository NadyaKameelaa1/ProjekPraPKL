<?php
require_once '../koneksi/koneksi.php';

header('Content-Type: application/json');

// Validate input
if (!isset($_GET['id_pesanan'])) {
    echo json_encode(['success' => false, 'message' => 'ID Pesanan diperlukan']);
    exit;
}

$id_pesanan = (int)$_GET['id_pesanan'];

// Get payment data
$query = "SELECT pb.upload_bukti, pb.metode_pembayaran 
          FROM pembayaran pb
          JOIN pesanan p ON pb.id_pesanan = p.id_pesanan
          WHERE p.id_pesanan = ?";
          
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $id_pesanan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'message' => 'Data pembayaran tidak ditemukan']);
    exit;
}

$data = mysqli_fetch_assoc($result);

// Handle different payment methods
if ($data['metode_pembayaran'] === 'Bayar di hotel') {
    echo json_encode([
        'success' => true,
        'payment_type' => 'hotel',
        'message' => 'Pembayaran akan dilakukan di hotel'
    ]);
    exit;
}

if (empty($data['upload_bukti'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Bukti pembayaran belum diupload'
    ]);
    exit;
}

// Verify image exists
$imagePath = '/JAVAST/Admin/Gambar/Bukti/' . $data['upload_bukti'];
$fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

if (!file_exists($fullPath)) {
    echo json_encode([
        'success' => false,
        'message' => 'File bukti pembayaran tidak ditemukan'
    ]);
    exit;
}

// Return image data
echo json_encode([
    'success' => true,
    'payment_type' => 'online',
    'image_url' => $imagePath
]);
?>