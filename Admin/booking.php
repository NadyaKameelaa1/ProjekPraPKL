<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'complete' && isset($_GET['id_pembayaran'])) {
    $id_pembayaran = (int)$_GET['id_pembayaran'];
    
    $update_query = "UPDATE pembayaran 
                    SET booking_status = 'Selesai' 
                    WHERE id_pembayaran = ?";
    
    $stmt = mysqli_prepare($koneksi, $update_query);
    mysqli_stmt_bind_param($stmt, 'i', $id_pembayaran);
    mysqli_stmt_execute($stmt);
    
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['success_message'] = "Status booking berhasil diubah menjadi Selesai";
    } else {
        $_SESSION['error_message'] = "Gagal mengubah status booking";
    }
    
    header("Location: booking.php?selesaikan=berhasil");
    exit;
}


$query = "SELECT p.*,
    h.nama_hotel,
    k.nama_kamar, k.harga_kamar,
    u.nama_user, u.no_telp,
    py.id_pembayaran, py.metode_pembayaran, py.booking_status, py.tanggal_bayar, py.id_order
    FROM pesanan p
    JOIN hotels h ON p.id_hotel = h.id_hotel
    JOIN kamar k ON p.id_kamar = k.id_kamar
    JOIN users u ON p.id_user = u.id_user
    JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    WHERE py.booking_status IN ('Dibayar', 'Dibatalkan', 'Selesai')
    ORDER BY p.tanggal_pesan DESC";

$result = mysqli_query($koneksi, $query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Booking | Javast - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="booking.css">

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
            <div class="menu-item active" onclick="window.location.href='booking.php'">
                <i class="fas fa-calendar-check"></i> Booking <i class="fa-solid fa-caret-down"></i>
            </div>
            <div class="menu-item" onclick="window.location.href='booking_terbaru.php'">
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
        <h1>BOOKING</h1>
    </div>
    <div class="header-2">
        <h1>Riwayat booking</h1>
    </div>

    <div class="table-controls">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="userSearch" placeholder="Cari id pengirim, nama pengirim, email pengirim, pesan, waktu,...">
        </div>

    </div>
    
    
    <div class="main-content">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error_message'] ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="table-container">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Id Pembayaran</th>
                    <th>Id Pesanan</th>
                    <th>Id Hotel</th>
                    <th>Detail User</th>
                    <th>Detail Kamar</th>
                    <th>Detail Booking</th>
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
                    <td><?= htmlspecialchars($pesanan['id_pembayaran']) ?></td>
                    <td><?= htmlspecialchars($pesanan['id_pesanan']) ?></td>
                    <td><?= htmlspecialchars($pesanan['id_hotel']) ?></td>
                    <td>
                        <span class="order-id">ID ORDER: <?= htmlspecialchars($pesanan['id_order']) ?></span><br>
                        <strong>Nama:</strong> <?= htmlspecialchars($pesanan['nama_user']) ?><br>
                        <strong>No. Telp:</strong> <?= htmlspecialchars($pesanan['no_telp']) ?>
                    </td>
                    <td>
                        <strong>Kamar:</strong> <?= htmlspecialchars($pesanan['nama_kamar']) ?><br>
                        <strong>Harga:</strong> Rp. <?= number_format($pesanan['harga_kamar'], 0, ',', '.') ?>
                    </td>
                    <td>
                        <strong>Check-In:</strong> <?= $check_in ?><br>
                        <strong>Check-Out:</strong> <?= $check_out ?>
                        <br>
                            <strong>Bayar:</strong> Rp. <?= number_format($pesanan['total_bayar'], 0, ',', '.') ?><br>
                            <strong>Waktu:</strong>  <?= htmlspecialchars($pesanan['tanggal_bayar']) ?>
                
                    </td>
                   
                    <td class="status-cell">
                            <span class="status <?php
                                if ($pesanan['booking_status'] == 'Dibayar') {
                                    echo 'paid';
                                } elseif ($pesanan['booking_status'] == 'Dibatalkan') {
                                    echo 'cancelled';
                                } elseif ($pesanan['booking_status'] == 'Selesai') {
                                    echo 'selesai';
                                }    else {
                                    echo 'cancelled';
                                }
                                ?>">
                                <?= htmlspecialchars($pesanan['booking_status']) ?>
                            </span>
                    </td>

                    <td>
                            <?php if ($pesanan['booking_status'] == 'Dibayar'): ?>
                                <a href="booking.php?action=complete&id_pembayaran=<?= $pesanan['id_pembayaran'] ?>" 
                                   class="action-btn selesai-btn" 
                                   onclick="return confirm('Apakah Anda yakin ingin menyelesaikan booking ini?')">
                                    Selesaikan
                                </a>
                            <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
        
            </tbody>
        </table>
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
</script>

</body>
</html>