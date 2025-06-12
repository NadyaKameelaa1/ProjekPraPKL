<?php
session_start();
require_once '../koneksi/koneksi.php';
require_once 'C:\xampp\htdocs\JAVAST\TCPDF-main\tcpdf.php'; // Sesuaikan path dengan lokasi TCPDF

// Perubahan: Tambahkan parameter view_mode
$view_mode = isset($_GET['view_mode']) ? $_GET['view_mode'] : 'D'; // Default download

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$id_pesanan = $_GET['id_pesanan'];
$email = $_SESSION['email_user'];

// Verifikasi kepemilikan transaksi dan ambil data
$query = "SELECT p.*, h.nama_hotel, h.kota_hotel, k.nama_kamar, k.harga_kamar,
          py.metode_pembayaran, py.booking_status, py.id_order, py.tanggal_bayar,
          u.nama_user
          FROM pesanan p
          JOIN hotels h ON p.id_hotel = h.id_hotel
          JOIN kamar k ON p.id_kamar = k.id_kamar
          JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
          JOIN users u ON p.id_user = u.id_user
          WHERE p.id_pesanan = ? AND u.email_user = ?";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("is", $id_pesanan, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Transaksi tidak ditemukan atau tidak memiliki akses");
}

$transaksi = $result->fetch_assoc();

// Format tanggal
$check_in = date('d-m-Y', strtotime($transaksi['check_in']));
$check_out = date('d-m-Y', strtotime($transaksi['check_out']));
$tanggal_bayar = date('d-m-Y', strtotime($transaksi['tanggal_bayar']));

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('JAVAST Hotel');
$pdf->SetAuthor('JAVAST Hotel');
$pdf->SetTitle('Kuitansi Booking Hotel');
$pdf->SetSubject('Kuitansi Pembayaran');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 16);

// Judul
$pdf->Cell(0, 10, 'JAVAST', 0, 1, 'C');
$pdf->Cell(0, 5, 'Booking Hotel Jawa Tengah', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Kuitansi Riwayat Booking Hotel di JAVAST', 0, 1, 'C');

// Garis pemisah
$pdf->SetLineWidth(0.5);
$pdf->Line(15, 40, 195, 40);
$pdf->Ln(10);

// Set font untuk konten
$pdf->SetFont('helvetica', '', 12);

// Data transaksi
$html = <<<EOD
<table cellspacing="0" cellpadding="5" border="0">
    <tr>
        <td width="30%"><strong>Nama Pemesan</strong></td>
        <td width="70%">{$transaksi['nama_user']}</td>
    </tr>
    <tr>
        <td><strong>ID Order</strong></td>
        <td>{$transaksi['id_order']}</td>
    </tr>
    <tr>
        <td><strong>Kota</strong></td>
        <td>{$transaksi['kota_hotel']}</td>
    </tr>
    <tr>
        <td><strong>Nama Hotel</strong></td>
        <td>{$transaksi['nama_hotel']}</td>
    </tr>
    <tr>
        <td><strong>Tipe Kamar</strong></td>
        <td>{$transaksi['nama_kamar']}</td>
    </tr>
    <tr>
        <td><strong>Harga Kamar per Malam</strong></td>
        <td>Rp. {$transaksi['harga_kamar']}</td>
    </tr>
    <tr>
        <td><strong>Check-in</strong></td>
        <td>{$check_in}</td>
    </tr>
    <tr>
        <td><strong>Check-out</strong></td>
        <td>{$check_out}</td>
    </tr>
    <tr>
        <td><strong>Total Pembayaran</strong></td>
        <td>Rp. {$transaksi['total_bayar']}</td>
    </tr>
    <tr>
        <td><strong>Tanggal Pembayaran</strong></td>
        <td>{$tanggal_bayar}</td>
    </tr>
    <tr>
        <td><strong>Metode Pembayaran</strong></td>
        <td>{$transaksi['metode_pembayaran']}</td>
    </tr>
    <tr>
        <td><strong>Status</strong></td>
        <td>{$transaksi['booking_status']}</td>
    </tr>
</table>

<br><br>
<div style="text-align: center;">
    <p>Terima kasih telah memesan di JAVAST!</p>
    <p>Kuitansi ini sah dan dapat digunakan sebagai bukti resmi transaksi.</p>
</div>
EOD;

// Output HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// // Garis tanda tangan
// // $pdf->SetY(-50);
// // $pdf->Line(50, $pdf->GetY(), 160, $pdf->GetY());
// // $pdf->SetFont('helvetica', 'I', 10);
// // $pdf->Cell(0, 5, 'Tanda Tangan', 0, 1, 'C');

// // Close and output PDF document
// $pdf->Output('kuitansi_'.$transaksi['id_order'].'.pdf', 'D');
// Modifikasi output berdasarkan mode
if($view_mode == 'qr_access') {
    // Untuk akses via QR code, tampilkan langsung di browser
    $pdf->Output('kuitansi_'.$transaksi['id_order'].'.pdf', 'I');
} else {
    // Untuk download manual
    $pdf->Output('kuitansi_'.$transaksi['id_order'].'.pdf', 'D');
}
?>