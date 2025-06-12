<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

// Query untuk mengambil data pesanan
$query = "SELECT p.*,
    h.nama_hotel,
    k.nama_kamar, k.harga_kamar,
    u.nama_user, u.no_telp,
    py.metode_pembayaran, py.booking_status, py.tanggal_bayar
    FROM pesanan p
    JOIN hotels h ON p.id_hotel = h.id_hotel
    JOIN kamar k ON p.id_kamar = k.id_kamar
    JOIN users u ON p.id_user = u.id_user
    JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    WHERE py.booking_status IN ('Belum dibayar', 'Menunggu Konfirmasi Admin')
    ORDER BY p.tanggal_pesan DESC";

$result = mysqli_query($koneksi, $query);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Terbaru | Javast - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="booking_terbaru.css">

</head>
<body>
    
    <div class="navbar">
        <div class="logo">
            <img src="logo/Logo_Javast.png" alt="Logo_Javast">

        </div>
        <div>
            <button class="button" onclick="window.location.href='login.php'"> 
                <i class="fas fa-user"></i> Log Out
            </button>
        </div>

    </div>
    
<div class="dashboard">
    <div class="sidebar">
        <div class="sidebar-header">
            <br><br><br><br><br>
            <h1><b>ADMIN PANEL</b></h1>
        </div>
        <div class="sidebar-menu">
            <div class="menu-item" onclick="window.location.href='dashboard.php'">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </div>
            <div class="menu-item" onclick="window.location.href='users.php'">
                <i class="fas fa-users"></i> Users
            </div>
            <div class="menu-item" onclick="window.location.href='hotels.php'">
                <i class="fas fa-hotel"></i> Hotel
            </div>
            <div class="menu-item" onclick="window.location.href='kamar.php'">
                <i class="fas fa-bed"></i> Kamar
            </div>
            <div class="menu-item" onclick="window.location.href='kontak.php'">
                <i class="fas fa-envelope"></i> Kontak Kami
            </div>
            <div class="menu-item" onclick="window.location.href='statistik.php'">
                <i class="fa-solid fa-chart-simple"></i> Statistik
            </div>
            <div class="menu-item" onclick="window.location.href='booking.php'">
                <i class="fas fa-calendar-check"></i> Booking <i class="fa-solid fa-caret-down"></i>
            </div>
            <div class="menu-item active" onclick="window.location.href='booking_terbaru.php'">
                <i class="fa-solid fa-circle-user"></i> Booking Terbaru
            </div>
    
        </div>
    </div>
</div>

<div class="main-content">
    <br>
    <br>
    <br>
    <br>
    <div class="header">
        <h1>BOOKING TERBARU</h1>
    </div>
    <div class="header-2">
        <h1>Pesanan dan Pembayaran</h1>
    </div>

    <div class="table-controls">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="userSearch" placeholder="Cari id pengirim, nama pengirim, email pengirim, pesan, waktu,...">
        </div>

    </div>
    
    

    <div class="table-container">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Id Pesan</th>
                    <th>Id Hotel</th>
                    <th>Detail User</th>
                    <th>Detail Kamar</th>
                    <th>Detail Booking</th>
                    <th>Metode Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                
                    <?php while($pesanan = mysqli_fetch_assoc($result)): ?>
                        <?php
                        // Format tanggal
                        $check_in = date('d-m-Y', strtotime($pesanan['check_in']));
                        $check_out = date('d-m-Y', strtotime($pesanan['check_out']));
                        // $tanggal_bayar = date('d-m-Y', strtotime($pesanan['tanggal_bayar']));
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($pesanan['id_pesanan']) ?></td>
                            <td><?= htmlspecialchars($pesanan['id_hotel']) ?></td>
                            <td>
                                <strong>Nama:</strong> <?= htmlspecialchars($pesanan['nama_user']) ?><br>
                                <strong>No. Telp:</strong> <?= htmlspecialchars($pesanan['no_telp']) ?>
                            </td>
                            <td>
                                <strong>Kamar:</strong> <?= htmlspecialchars($pesanan['nama_kamar']) ?><br>
                                <strong>Harga:</strong> Rp. <?= number_format($pesanan['harga_kamar'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <strong>Check-In:</strong> <?= $check_in ?><br>
                                <strong>Check-Out:</strong> <?= $check_out ?><br>
                                <strong>Bayar:</strong> Rp. <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?><br>
                                <strong>Waktu:</strong> <?= htmlspecialchars($pesanan['tanggal_bayar']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($pesanan['metode_pembayaran']) ?>
                            </td>
                            <td>
                                <span class="status <?php
                                if ($pesanan['booking_status'] == 'Menunggu Konfirmasi Admin') {
                                    echo 'pending';
                                } elseif ($pesanan['booking_status'] == 'Dibatalkan') {
                                    echo 'cancelled';
                                } elseif ($pesanan['booking_status'] == 'Dibayar') {
                                    echo 'paid';
                                } elseif ($pesanan['booking_status'] == 'Belum dibayar') {
                                    echo 'pending';
                                } else {
                                    echo 'pending';
                                }
                            ?>">
                                <?= htmlspecialchars($pesanan['booking_status']) ?>
                            </span>
                            </td>
                            <td>
                            <?php if ($pesanan['booking_status'] == 'Belum dibayar' || $pesanan['booking_status'] == 'Menunggu Konfirmasi Admin'): ?>
                                <button class="action-btn verify-btn" onclick="verifyBooking(<?= $pesanan['id_pesanan'] ?>)">
                                    <i class="fa-solid fa-user-check"></i> Verifikasi
                                </button>
                            <?php endif; ?>

                            <?php if ($pesanan['booking_status'] == 'Belum dibayar' || $pesanan['booking_status'] == 'Menunggu Konfirmasi Admin'): ?>
                            <button class="action-btn cancel-btn" onclick="cancelBooking(<?= $pesanan['id_pesanan'] ?>)">
                                <i class="fa-solid fa-user-xmark"></i> Batal
                            </button>
                            <?php endif; ?>
                            
                            <button class="action-btn bukti-btn" onclick="viewPaymentProof(<?= $pesanan['id_pesanan'] ?>)">
                            <i class="fa-solid fa-dollar-sign"></i> Bukti pembayaran
                            </button>
                        </td>
                        </tr>
                    <?php endwhile; ?>
                    
                


            </tbody>
        </table>
    </div>

<div id="paymentProofModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" onclick="window.location.href='booking_terbaru.php'">&times;</span>
    <h2>Bukti Pembayaran</h2>
    
    <div class="payment-info">
      <p><span id="orderId"></span></p>
      <p><span id="paymentMethod"></span></p>
      <p><span id="paymentStatus"></span></p>
      <p><span id="paymentDate"></span></p>
    </div>
    
    <div id="proofImageContainer">
      <!-- Content will be loaded here -->
    </div>
  </div>
</div>

</div>
</div>

</div>

<script>
    document.getElementById('userSearch').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.crud-table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});


//---------------

function verifyBooking(id) {
            if(confirm('Apakah Anda yakin ingin memverifikasi pesanan ini?')) {
                window.location.href = 'proses_verifikasi.php?id_pesanan=' + id;
            }
        }
        
        function cancelBooking(id) {
            if(confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
                window.location.href = 'proses_batal.php?id_pesanan=' + id;
            }
        }
        


        // -------
function viewPaymentProof(id_pesanan) {
    // Show loading state
    document.getElementById('proofImageContainer').innerHTML = '<p>Memuat data pembayaran...</p>';
    document.getElementById('orderId').textContent = '';
    document.getElementById('paymentDate').textContent = '';
    document.getElementById('paymentMethod').textContent = '';
    document.getElementById('paymentStatus').textContent = '';
    
    fetch('booking_bukti_pembayaran.php?id_pesanan=' + id_pesanan)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update basic info
                document.getElementById('orderId').textContent = data.id_pesanan;
                document.getElementById('paymentDate').textContent = data.tanggal_bayar;
                document.getElementById('paymentMethod').textContent = data.metode_pembayaran;
                document.getElementById('paymentStatus').textContent = data.status;
                
                // Handle different payment methods
                if (data.is_hotel_payment) {
                    // Hotel payment - no proof needed
                    document.getElementById('proofImageContainer').innerHTML = 
                        '<div class="hotel-payment">' +
                        '<i class="fa-solid fa-hotel"></i>' +
                        '<p>' + data.message + '</p>' +
                        '</div>';
                } else {
                    // Online payment - show proof
                    const img = document.createElement('img');
                    img.src = data.image_url;
                    img.alt = 'Bukti Pembayaran';
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '500px';
                    img.onerror = function() {
                        document.getElementById('proofImageContainer').innerHTML = 
                            '<div class="error-message">' +
                            '<i class="fa-solid fa-image"></i>' +
                            '<p>Gagal memuat bukti pembayaran</p>' +
                            '</div>';
                    };
                    
                    const container = document.getElementById('proofImageContainer');
                    container.innerHTML = '';
                    container.appendChild(img);
                }
                
                // Show modal
                document.getElementById('paymentProofModal').style.display = 'block';
            } else {
                document.getElementById('proofImageContainer').innerHTML = 
                    '<div class="error-message">' +
                    '<i class="fa-solid fa-circle-exclamation"></i>' +
                    '<p>' + data.message + '</p>' +
                    '</div>';
                document.getElementById('paymentProofModal').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('proofImageContainer').innerHTML = 
                '<div class="error-message">' +
                '<i class="fa-solid fa-circle-exclamation"></i>' +
                '<p>Terjadi kesalahan saat memuat data pembayaran</p>' +
                '</div>';
            document.getElementById('paymentProofModal').style.display = 'block';
        });
}
</script>

</body>
</html>