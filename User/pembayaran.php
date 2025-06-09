<?php
session_start();
require_once '../koneksi/koneksi.php';

// Cek login
if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}
// Ambil parameter
$id_hotel = isset($_GET['id_hotel']) ? intval($_GET['id_hotel']) : 0;
$id_kamar = isset($_GET['id_kamar']) ? intval($_GET['id_kamar']) : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d');
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'));
$dewasa = isset($_GET['dewasa']) ? intval($_GET['dewasa']) : 1;
$anak = isset($_GET['anak']) ? intval($_GET['anak']) : 0;
$jumlah_kamar = isset($_GET['kamar']) ? intval($_GET['kamar']) : 1;
$jumlah_hari = isset($_GET['jumlah_hari']) ? intval($_GET['jumlah_hari']) : 0;
$total_bayar = isset($_GET['total_bayar']) ? intval($_GET['total_bayar']) : 0;

$id_pesanan = intval($_GET['id_pesanan']);

// Ambil data kamar
$query_kamar = mysqli_query($koneksi, "SELECT k.*, kg.gambarA, kg.gambarB, kg.gambarC, kg.gambarD, kg.gambarE, h.nama_hotel 
                                     FROM kamar k 
                                     JOIN hotels h ON k.id_hotel = h.id_hotel 
                                     LEFT JOIN kamar_gambar kg ON k.id_kamar = kg.id_kamar 
                                     WHERE k.id_kamar = $id_kamar");
$detail_kamar = mysqli_fetch_assoc($query_kamar);

if (!$detail_kamar) {
    die("Kamar tidak ditemukan!");
}


$query = mysqli_query($koneksi, "SELECT hotels.*, MIN(kamar.harga_kamar) AS harga_terendah 
    FROM hotels
    LEFT JOIN kamar ON hotels.id_hotel = kamar.id_hotel
    WHERE hotels.id_hotel = $id_hotel
    GROUP BY hotels.id_hotel");
$hotels = mysqli_fetch_assoc($query);

// IMPORTANT: Calculate total payment here
// $harga_kamar = $detail_kamar['harga_kamar'];

// Calculate number of days if not provided
if ($jumlah_hari <= 0) {
    $harga_kamar = isset($hotels['harga_kamar']) ? $hotels['harga_kamar'] : 0;
    $jumlah_hari = (new DateTime($check_out))->diff(new DateTime($check_in))->days;
}

// Recalculate total payment
// $recalculated_total = $harga_kamar * $jumlah_hari * $jumlah_kamar;

// Use recalculated total if it differs from the passed parameter
// if ($total_bayar != $recalculated_total || $total_bayar == 0) {
//     $total_bayar = $recalculated_total;
// }

$query_pesanan = "SELECT p.*, k.nama_kamar, k.harga_kamar, h.nama_hotel 
                  FROM pesanan p 
                  JOIN kamar k ON p.id_kamar = k.id_kamar 
                  JOIN hotels h ON p.id_hotel = h.id_hotel 
                  WHERE p.id_pesanan = ?";

$stmt = $koneksi->prepare($query_pesanan);
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

if (!$pesanan) {
    die("Kamar tidak ditemukan atau data pesanan tidak valid!");
}
// Ambil data user
$email = $_SESSION['email_user'];
$query_user = mysqli_query($koneksi, "SELECT nama_user, no_telp, alamat_user FROM users WHERE email_user = '$email'");
$user = mysqli_fetch_assoc($query_user);


$user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['nama_user'] ?? 'User';
// Gunakan data dari database
 $jumlah_hari = $pesanan['jumlah_hari'];
 $total_bayar = $pesanan['total_bayar'];


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran <?= $hotels['nama_hotel'];?> | Javast</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="pembayaran.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    
    <div class="navbar">
        <div class="logo">
            <img src="logo/Logo_Javast.png" alt="Logo_Javast">

        </div>
        <div class="menu">
            <a href="home.php">Beranda</a>
            <a href="tentang.php">Tentang</a>
            <a href="kontak_kami.php">Kontak Kami</a>

        </div>

        <div class="dropdown">
            <button class="dropdown-btn"> 
                <i class="fas fa-user"></i> <?= htmlspecialchars($username) ?> ▼
            </button>
            <div class="dropdown-menu">
                <a href="profil.php">Profil</a>
                <a href="booking.php">Booking</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
        
    </div>
    
    <br>
    <br>

    <!-- bayar -->

    <div class="steps">
        <div class="step active">Pesan</div>
        <div class="arrow"><i class="fa-solid fa-right-long"></i></div>
        <div class="step active">Bayar</div>
        <div class="arrow"><i class="fa-solid fa-right-long"></i></div>
        <div class="step">Upload Bukti</div>
        <div class="arrow"><i class="fa-solid fa-right-long"></i></div>
        <div class="step">Tunggu</div>
      </div>
    
      <div class="container">
        <div class="card">
          <?php if (!empty($detail_kamar['gambarA'])): ?>
                <img src="/JAVAST/Admin/Gambar/Kamar/<?= htmlspecialchars($detail_kamar['gambarA']) ?>" alt="Kamar Hotel" width="300">
            <?php else: ?>
                <img src="gambar/default-room.jpg" alt="Kamar Default" width="300">
            <?php endif; ?> 
          <br>
          <h4><b><?= htmlspecialchars($detail_kamar['nama_kamar']) ?></b></h4>
            <p class="price">Rp. <?= number_format($detail_kamar['harga_kamar'], 0, ',', '.') ?></p>
    
          <div class="metode-pembayaran">
            <h5><b>Metode Pembayaran</b></h5>
            <p>Pilih salah satu metode pembayaran dibawah :</p>
            <hr>
            <div class="card-opsi">
                <div class="opsi-metode">
                  <label for="popupBukti" class="metode-btn" onclick="setMetode('BRI')">
                      <img src="gambar/pembayaran/logo_BRI.png" alt="BANK_BRI">
                  </label>
                  <label for="popupBukti" class="metode-btn" onclick="setMetode('BCA')">
                      <img src="gambar/pembayaran/logo-bank-bca.png" alt="BCA">
                  </label>
                  <label for="popupBukti" class="metode-btn" onclick="setMetode('DANA')">
                      <img src="gambar/pembayaran/logo-bank-dana.png" alt="DANA">
                  </label>
                  <label for="popupBukti" class="metode-btn" onclick="setMetode('GOPAY')">
                      <img src="gambar/pembayaran/logo-gopay-vector.png" alt="GOPAY">
                  </label>
              </div>    
            </div>

            <div class="atau">Atau</div>
            <div class="card-byr-hotel">
              <form action="proses_bayar_hotel.php" method="post">
                  <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
                  <button class="bayar-di-hotel">Bayar di hotel</button>
              </form>
            </div>
          </div>
        
        </div>
                <input type="hidden" name="id_hotel" value="<?= $id_hotel ?>">
                <input type="hidden" name="id_kamar" value="<?= $id_kamar ?>">
                <input type="hidden" name="check_in" value="<?= $check_in ?>">
                <input type="hidden" name="check_out" value="<?= $check_out ?>">
                <input type="hidden" id="hiddenDewasa" name="dewasa" value="<?= $dewasa ?>">
                <input type="hidden" id="hiddenAnak" name="anak" value="<?= $anak ?>">
                <input type="hidden" id="hiddenKamar" name="kamar" value="<?= $jumlah_kamar ?>">
                <input type="hidden" id="total_bayar" name="total_bayar" value="<?= $total_bayar ?>">
        <div class="order-box">
          <h4><b>Bayar</b></h4>
          <form>
            <label>Nama</label>
            <input type="text" value="<?= htmlspecialchars($user['nama_user']) ?>" readonly>
    
            <label>No. Telp</label>
            <input type="text" value="<?= htmlspecialchars($user['no_telp']) ?>" readonly>
    
            <label>Alamat</label>
            <input type="text" value="<?= htmlspecialchars($user['alamat_user']) ?>" readonly>
    
            <label>Tamu</label>
            
              <input type="text" value="<?= "{$pesanan['jumlah_dewasa']} Dewasa, {$pesanan['jumlah_anak']} Anak, {$pesanan['jumlah_kamar']} Kamar" ?>" readonly>
         
    
            <div class="date-group">
              <div>
                <label>Check In</label>
                <input type="date" name="check_in" value="<?= $check_in ?>" class="date-input" readonly>
              </div>
              <div>
                <label>Check Out</label>
                <input type="date"name="check_out" value="<?= $check_out ?>" class="date-input" readonly>
              </div>
            </div>
              <br>
              <br><br><br><br><br>
            <p>Jumlah Hari: <strong><?= $jumlah_hari ?></strong></p>
            <p class="total">Total Bayar : <span id="total_bayar">Rp. <?= number_format($total_bayar, 0, ',', '.') ?></span></p>
            
          </form>
        </div>
      </div>
    
    
      <!-- Tambahkan ini di bagian atas body -->
<input type="checkbox" id="popupBukti" class="hidden-checkbox">

<div class="popup-overlay"></div>
<div class="popup-upload">
    <div class="popup-content">
        <h4><b>Upload Bukti</b></h4>
        <p>Pastikan bukti yang diupload sesuai dengan metode pembayaran yang dipilih karena bukti butuh di verifikasi oleh admin.</p>
        
        <form action="proses_upload.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="metode_pembayaran" id="metodePembayaran">
            <input type="hidden" name="id_pesanan" value="<?= $_GET['id_pesanan'] ?>">
            
            <label>Pilih bukti foto atau screenshot :</label>
            <input type="file" name="bukti_pembayaran" required accept="image/*">
            
            <button type="submit" name="submit" class="btn-submit">Upload Bukti</button>
        </form>
        
        <label for="popupBukti" class="close-popup">×</label>
    </div>
</div>


      <br>
      <br>
      <br>
      <br>


    <!-- footer -->

    <footer>
        <div class="footer-container">
            <div class="footer-logo">
                
                <img src="logo/Logo_Javast.png" alt="Javast Logo">
                <p>Selamat datang di platform Javast, tempat terbaik untuk memesan hotel
                    hampir di seluruh kota di Jawa Tengah. Website ini
                    dirancang khusus untuk memudahkan Anda dalam menemukan
                    dan memesan akomodasi sesuai dengan kebutuhan, baik untuk keperluan bisnis maupun liburan.</p>
            </div>
    
            <div class="footer-links">
                <h3>Link</h3>
                <ul>
                    <li><a href="home.php">Beranda</a></li>
                    <li><a href="tentang.php">Tentang</a></li>
                    <li><a href="kontak_kami.php">Kontak Kami</a></li>
                </ul>
            </div>
    
            <div class="footer-social">
                <h3>Ikuti Kami</h3>
                <ul>
                    <li><a href="#"><i class="fab fa-facebook"></i> Javast</a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i> @javast.hotel</a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i> @javast.hotel</a></li>
                </ul>
            </div>
        </div>
    </footer>


    <script>
function setMetode(metode) {
    document.getElementById('metodePembayaran').value = metode;
    document.querySelector('.popup-content h4').textContent = `Upload Bukti Pembayaran ${metode}`;
    
    // Set header popup berdasarkan metode
    const header = document.querySelector('.popup-content h4');
    header.innerHTML = `Upload Bukti Pembayaran <span style="color: #FFA500">${metode}</span>`;
    header.style.marginBottom = '10px';
}

const bookingData = {
    roomPrice: <?= $harga_kamar ?>,
    numberOfRooms: <?= $jumlah_kamar ?>,
    numberOfDays: <?= $jumlah_hari ?>,
    totalPayment: <?= $total_bayar ?>, // This will now have the correct calculated value
    checkIn: '<?= $check_in ?>',
    checkOut: '<?= $check_out ?>',
    adults: <?= $dewasa ?>,
    children: <?= $anak ?>
};

// Function to recalculate total if needed (for future dynamic updates)
function recalculatePaymentTotal() {
    const newTotal = bookingData.roomPrice * bookingData.numberOfRooms * bookingData.numberOfDays;
    bookingData.totalPayment = newTotal;
    
    // Update display
    document.querySelectorAll('.total span').forEach(span => {
        span.textContent = `Rp. ${number_format(newTotal, 0, ',', '.')}`;
    });
    
    // Update hidden input
    const totalInput = document.getElementById('total_bayar');
    if (totalInput) {
        totalInput.value = newTotal;
    }
}
// Function to format numbers (similar to PHP's number_format)
function number_format(number, decimals = 0, dec_point = ',', thousands_sep = '.') {
    const n = !isFinite(+number) ? 0 : +number;
    const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    const sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep;
    const dec = (typeof dec_point === 'undefined') ? ',' : dec_point;
    const toFixedFix = function(n, prec) {
        const k = Math.pow(10, prec);
        return '' + (Math.round(n * k) / k).toFixed(prec);
    };
    const s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
// Verify calculation on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Booking Data:', bookingData);
    console.log('Calculation: ' + bookingData.roomPrice + ' * ' + bookingData.numberOfRooms + ' * ' + bookingData.numberOfDays + ' = ' + bookingData.totalPayment);
    recalculatePaymentTotal();
});
</script>

</body>
</html>