<?php
session_start();
require_once '../Koneksi/Koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email_user'];
$user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['nama_user'] ?? 'User';

// Ambil parameter pencarian
$lokasi = isset($_GET['lokasi']) ? mysqli_real_escape_string($koneksi, $_GET['lokasi']) : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : date('Y-m-d');
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : date('Y-m-d', strtotime('+1 day'));
$dewasa = isset($_GET['dewasa']) ? intval($_GET['dewasa']) : 1;
$anak = isset($_GET['anak']) ? intval($_GET['anak']) : 0;
$kamar = isset($_GET['kamar']) ? intval($_GET['kamar']) : 1;

// Query pencarian hotel dengan filter kapasitas
// Query pencarian hotel yang sudah diperbaiki
$query = "SELECT
    h.*,
    MIN(k.harga_kamar) AS harga_terendah,
    COUNT(k.id_kamar) AS jumlah_kamar_tersedia
    FROM hotels h
    JOIN kamar k ON h.id_hotel = k.id_hotel
    WHERE (
        h.nama_hotel LIKE '%$lokasi%' OR 
        h.kota_hotel LIKE '%$lokasi%' OR
        h.alamat_hotel LIKE '%$lokasi%' OR
        h.lokasi_hotel LIKE '%$lokasi%'
    )
    AND h.id_hotel NOT IN (
        SELECT p.id_hotel FROM pesanan p
        LEFT JOIN pembayaran pb ON p.id_pembayaran = pb.id_pembayaran
        WHERE (
            (p.check_in <= '$check_in' AND p.check_out >= '$check_in') OR
            (p.check_in <= '$check_out' AND p.check_out >= '$check_out') OR
            (p.check_in >= '$check_in' AND p.check_out <= '$check_out')
        )
        AND pb.booking_status != 'Dibatalkan'
    )
    AND k.jumlah_dewasa >= $dewasa
    AND (k.jumlah_dewasa + k.jumlah_anak) >= ($dewasa + $anak)
    AND k.jumlah_kamar >= $kamar
    GROUP BY h.id_hotel
    ORDER BY h.bintang_hotel DESC, harga_terendah ASC";

$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}

$hotels = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="hasil_pencarian.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>


    <div class="navbar">
        <div class="logo">
            <img src="Logo/Logo_Javast.png" alt="Logo_Javast">

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

    <header class="header">
        <h5>Hotel Jawa Tengah</h5>
        <h2>Hasil Pencarian</h2>
        <hr>

    </header>

    <div class="search-bar">
        <div class="location-group">
               <label><i class="fa-solid fa-location-dot"></i></label> 
              <input type="text" class="location-input" id="locationInput" placeholder="Kota, hotel" autocomplete="off" value="<?= htmlspecialchars($lokasi) ?>">
              <div class="input-wrapper">
                <div id="locationDropdown">
                    <h4 class="dropdown-title">Kota yang tersedia</h4>
                <hr />
                
     </div>
     </div>
</div>
           
                <div class="date-box">
              <input type="date"  class="date-input" placeholder="Check-in" value="<?= $check_in ?>">
              </div>

              <div class="date-box">
              <input type="date"  class="date-input" placeholder="Check-out" value="<?= $check_out ?>">   
              </div>
    
              <label><i class="fa-solid fa-user-check"></i> </label>
              <div class="guest-summary">
                <?= $_GET['dewasa'] ?? '1' ?> Dewasa, 
                <?= $_GET['anak'] ?? '0' ?> Anak, 
                <?= $_GET['kamar'] ?? '1' ?> Kamar
              </div>
       

        <button onclick="window.location.href='home.php?pesan=IsiSearchBarDisini!'"><i class="fa-solid fa-magnifying-glass"></i></button>
    </div>
    
    
    <?php if (count($hotels) > 0): ?>
    <div class="container-hotel">
        <?php foreach ($hotels as $hotel): ?>
            <div class="hotel-card">
                <div class="hotel-image">
                    <img src="/JAVAST/Admin/Gambar/Hotel/<?= htmlspecialchars($hotel['gambar_hotel']) ?>" alt="<?= htmlspecialchars($hotel['nama_hotel']) ?>">
                </div>
                
                <div class="hotel-info">
                    <h2><b><?= htmlspecialchars($hotel['nama_hotel']) ?></b></h2>
                    <div class="rating">Bintang <?= $hotel['bintang_hotel'] ?>
                        <span class="stars"><?= str_repeat('★', $hotel['bintang_hotel']) ?></span>
                    </div>
                    <div class="location">
                        <i class="fa-solid fa-location-dot"></i>
                        <?= htmlspecialchars($hotel['kota_hotel']) ?>, Jawa Tengah<br>
                        <?= htmlspecialchars($hotel['alamat_hotel']) ?>
                    </div>
                    <div class="facilities">
                        <?php
                        $fitur = array_slice(explode(',', $hotel['fasilitas_hotel']), 0, 6);
                        foreach ($fitur as $item):
                            $item = trim($item);
                            if (!empty($item)):
                        ?>
                        <span><?= htmlspecialchars($item) ?></span>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
                
                <div class="hotel-booking">
                    <div class="price">1 Malam <br><strong>Rp <?= number_format($hotel['harga_terendah'], 0, ',', '.') ?></strong></div>
                    <div class="note">Di luar pajak & biaya</div>
                    <div>
                        <a href="hotel.php?id_hotel=<?= $hotel['id_hotel'] ?>&check_in=<?= $check_in ?>&check_out=<?= $check_out ?>&dewasa=<?= $dewasa ?>&anak=<?= $anak ?>&kamar=<?= $kamar ?>">
                            <button class="button">Pilih Kamar</button>
                        </a>
                    </div>
                    
                </div>
                
            </div>
        <?php endforeach; ?>
        
    </div>
    
    
    <?php else: ?>
    <div class="no-results">
        <p>Maaf, tidak ditemukan hotel yang sesuai dengan kriteria pencarian Anda.</p>
    </div>
    <?php endif; ?>
    
    <footer>
        <div class="footer-container">
            <div class="footer-logo">
                
                <img src="Logo/Logo_Javast.png" alt="Javast Logo">
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
                    <li><a href="kontak_kami.php">Kontak Kami Us</a></li>
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
        // Guest Room Dropdown Functionality

        const trigger = document.getElementById('guestRoomTrigger');
        const dropdown = document.getElementById('guestRoomDropdown');
        
        trigger.addEventListener('click', function(e) {
            if (e.target.closest('.guest-room-dropdown')) return;
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
        
        document.addEventListener('click', function(e) {
            if (!trigger.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
        


        // Guest Counter Functionality
        document.querySelectorAll('.counter-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                
                const counter = this.parentElement;
                const valueElement = counter.querySelector('.counter-value');
                let value = parseInt(valueElement.textContent);
                
                if (this.textContent === '+') {
                    value++;
                } else {
                    if (value > 0) {
                        value--;
                    }
                }
                
                valueElement.textContent = value;
                
                // Update summary text
                const adultValue = parseInt(document.querySelectorAll('.counter-value')[0].textContent);
                const childValue = parseInt(document.querySelectorAll('.counter-value')[1].textContent);
                const roomValue = parseInt(document.querySelectorAll('.counter-value')[2].textContent);
                
                trigger.querySelector('.sub-label').textContent = 
                    `${adultValue} Dewasa, ${childValue} Anak, ${roomValue} Kamar`;
            });
        });
        
        document.querySelector('.apply-btn').addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.style.display = 'none';
        });



        // Date and Duration Functionality
        const checkInDate = document.getElementById('checkInDate');
        const checkOutDate = document.getElementById('checkOutDate');
        const increaseDuration = document.getElementById('increaseDuration');
        const decreaseDuration = document.getElementById('decreaseDuration');
        const durationValue = document.getElementById('durationValue');

        // Set default dates
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);
        
        checkInDate.valueAsDate = today;
        checkOutDate.valueAsDate = tomorrow;

        // Format tampilan tanggal (YYYY-MM-DD)
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // mengubah otomatis checkout
        function updateCheckOutDate() {
            if (!checkInDate.value) return;
            
            const duration = parseInt(durationValue.textContent);
            const startDate = new Date(checkInDate.value);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + duration);
            
            checkOutDate.value = formatDate(endDate);
        }

        // ngubah otomatis durasi 
        increaseDuration.addEventListener('click', function() {
            let duration = parseInt(durationValue.textContent);
            duration++;
            durationValue.textContent = duration;
            updateCheckOutDate();
        });

        decreaseDuration.addEventListener('click', function() {
            let duration = parseInt(durationValue.textContent);
            if (duration > 1) {
                duration--;
                durationValue.textContent = duration;
                updateCheckOutDate();
            }
        });

        // Update check-out pas check-in berubah
        checkInDate.addEventListener('change', updateCheckOutDate);
        
        updateCheckOutDate();


    // destinasi

    document.addEventListener("DOMContentLoaded", function() {
        const locationInput = document.getElementById("locationInput");
        const locationDropdown = document.getElementById("locationDropdown");
        const cityListItems = document.querySelectorAll(".city-list li");

      // Tampilkan/ sembunyikan dropdown saat klik input
    locationInput.addEventListener("click", function(e) {
        e.stopPropagation();
        locationDropdown.style.display = locationDropdown.style.display === "block" ? "none" : "block";
    });

      // Klik kota akan isi input dan tutup dropdown
    cityListItems.forEach(function(li) {
        li.addEventListener("click", function() {
          locationInput.value = li.getAttribute("data-city");
          locationDropdown.style.display = "none";
      });
    });

      // Klik di luar input & dropdown, tutup dropdown
    document.addEventListener("click", function(event) {
        if (!locationInput.contains(event.target) && !locationDropdown.contains(event.target)) {
          locationDropdown.style.display = "none";
        }
      });
    });
        


        // Guest Counter Functionality
        // document.querySelectorAll('.counter-btn').forEach(button => {
        //     button.addEventListener('click', function(e) {
        //         e.stopPropagation();
                
        //         const counter = this.parentElement;
        //         const valueElement = counter.querySelector('.counter-value');
        //         let value = parseInt(valueElement.textContent);
                
        //         if (this.textContent === '+') {
        //             value++;
        //         } else {
        //             if (value > 0) {
        //                 value--;
        //             }
        //         }
                
        //         valueElement.textContent = value;
                
        //         // Update summary text
        //         const adultValue = parseInt(document.querySelectorAll('.counter-value')[0].textContent);
        //         const childValue = parseInt(document.querySelectorAll('.counter-value')[1].textContent);
        //         const roomValue = parseInt(document.querySelectorAll('.counter-value')[2].textContent);
                
        //         trigger.querySelector('.sub-label').textContent = 
        //             `${adultValue} Dewasa, ${childValue} Anak, ${roomValue} Kamar`;
        //     });
        // });
        
        // document.querySelector('.apply-btn').addEventListener('click', function(e) {
        //     e.stopPropagation();
        //     dropdown.style.display = 'none';
        // });



        // Date and Duration Functionality
        // const checkInDate = document.getElementById('checkInDate');
        // const checkOutDate = document.getElementById('checkOutDate');
        // const increaseDuration = document.getElementById('increaseDuration');
        // const decreaseDuration = document.getElementById('decreaseDuration');
        // const durationValue = document.getElementById('durationValue');

        // Set default dates
        // const today = new Date();
        // const tomorrow = new Date();
        // tomorrow.setDate(today.getDate() + 1);
        
        // checkInDate.valueAsDate = today;
        // checkOutDate.valueAsDate = tomorrow;

        // Format tampilan tanggal (YYYY-MM-DD)
        // function formatDate(date) {
        //     const year = date.getFullYear();
        //     const month = String(date.getMonth() + 1).padStart(2, '0');
        //     const day = String(date.getDate()).padStart(2, '0');
        //     return `${year}-${month}-${day}`;
        // }

        // mengubah otomatis checkout
        // function updateCheckOutDate() {
        //     if (!checkInDate.value) return;
            
        //     const duration = parseInt(durationValue.textContent);
        //     const startDate = new Date(checkInDate.value);
        //     const endDate = new Date(startDate);
        //     endDate.setDate(startDate.getDate() + duration);
            
        //     checkOutDate.value = formatDate(endDate);
        // }

        // ngubah otomatis durasi 
        // increaseDuration.addEventListener('click', function() {
        //     let duration = parseInt(durationValue.textContent);
        //     duration++;
        //     durationValue.textContent = duration;
        //     updateCheckOutDate();
        // });

        // decreaseDuration.addEventListener('click', function() {
        //     let duration = parseInt(durationValue.textContent);
        //     if (duration > 1) {
        //         duration--;
        //         durationValue.textContent = duration;
        //         updateCheckOutDate();
        //     }
        // });

        // Update check-out pas check-in berubah
        // checkInDate.addEventListener('change', updateCheckOutDate);
        
        // updateCheckOutDate();


    // destinasi

    // document.addEventListener("DOMContentLoaded", function() {
    //     const locationInput = document.getElementById("locationInput");
    //     const locationDropdown = document.getElementById("locationDropdown");
    //     const cityListItems = document.querySelectorAll(".city-list li");

      // Tampilkan/ sembunyikan dropdown saat klik input
    // locationInput.addEventListener("click", function(e) {
    //     e.stopPropagation();
    //     locationDropdown.style.display = locationDropdown.style.display === "block" ? "none" : "block";
    // });

      // Klik kota akan isi input dan tutup dropdown
    // cityListItems.forEach(function(li) {
    //     li.addEventListener("click", function() {
    //       locationInput.value = li.getAttribute("data-city");
    //       locationDropdown.style.display = "none";
    //   });
    // });

      // Klik di luar input & dropdown, tutup dropdown
    // document.addEventListener("click", function(event) {
    //     if (!locationInput.contains(event.target) && !locationDropdown.contains(event.target)) {
    //       locationDropdown.style.display = "none";
    //     }
    //   });
    // });
    </script>

  
      

</body>
</html>
