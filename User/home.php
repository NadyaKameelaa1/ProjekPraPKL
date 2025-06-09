<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email_user'];
$user_query = mysqli_query($koneksi, "SELECT nama_user FROM users WHERE email_user = '$email'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['nama_user'] ?? 'User';

$query = mysqli_query($koneksi, "SELECT hotels.*, MIN(kamar.harga_kamar) AS harga_terendah 
    FROM hotels
    LEFT JOIN kamar ON hotels.id_hotel = kamar.id_hotel
    WHERE hotels.id_hotel IN (2027, 2028, 2029)
    GROUP BY hotels.id_hotel");

$hotels = [];
while ($row = mysqli_fetch_assoc($query)) {
    $hotels[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

    <header class="header">
        <h5>Hotel Jawa Tengah</h5>
        <h2>SELAMAT DATANG</h2>
        <hr>
    </header>
    <br>
    <br>

      <div class="search-container">
        <form action="hasil_pencarian.php" method="GET">
            <input type="hidden" name="dewasa" id="hiddenDewasa" value="2">
            <input type="hidden" name="anak" id="hiddenAnak" value="0">
            <input type="hidden" name="kamar" id="hiddenKamar" value="1">
            <div class="section-title">Kota, atau nama hotel</div>
            <input type="text" class="location-input" id="locationInput" name="lokasi" placeholder="Kota, hotel" autocomplete="off" required />
            
            <div class="input-wrapper">
            <div id="locationDropdown">
                <h4 class="dropdown-title">Kota yang tersedia :</h4>
                <hr />
                <ul class="city-list">
                    <?php
                    $query = "SELECT DISTINCT kota_hotel, 
                            (SELECT COUNT(*) FROM hotels WHERE kota_hotel = h.kota_hotel) as jumlah_hotel
                            FROM hotels h
                            ORDER BY jumlah_hotel DESC";
                    $result = mysqli_query($koneksi, $query);
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<li data-city="'.htmlspecialchars($row['kota_hotel']).'">';
                        echo htmlspecialchars($row['kota_hotel']).' <span>'.$row['jumlah_hotel'].' Hotel</span>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
            </div>
            <div class="section-title">
            <div class="label-container">
                <div class="check-in-label">Check-in</div>
                <div class="duration-label">Durasi</div>
                <div class="check-out-label">Check-out</div>
            </div>
            </div>

        <div class="date-container">
            <div class="date-box">
                <input type="date" class="date-input" id="checkInDate" name="check_in" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="duration-box">
                <div class="duration-counter">
                    <button type="button" class="duration-btn" id="decreaseDuration">-</button>
                    <span class="duration-value" id="durationValue">1</span>
                    <button type="button" class="duration-btn" id="increaseDuration">+</button>
                    <span class="malam">Malam</span>
                </div>
            </div>
            <div class="date-box">
                <input type="date" class="date-input" id="checkOutDate" name="check_out" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" readonly>
            </div>
        </div>

        <div class="section-title">Tamu dan Kamar</div>
        <div class="search-row">
        <div class="guest-room-box" id="guestRoomTrigger">
            <form action="hasil_pencarian.php" method="GET">
             <input type="hidden" id="dewasa" name="jumlah_dewasa" value="1">
            <input type="hidden" id="anak" name="jumlah_anak" value="0">
            <input type="hidden" id="kamar" name="jumlah_kamar" value="1">
            
            <div class="sub-label" id="guestRoomDisplay">1 Dewasa, 0 Anak, 1 Kamar</div>

            <div class="guest-room-dropdown" id="guestRoomDropdown">
                <div class="guest-room-item">   
                    <div class="guest-room-label">Dewasa</div>
                    <div class="counter">
                        <button type="button" class="counter-btn" onclick="updateCounter('dewasa', -1)" disabled>-</button>
                        <span class="counter-value" id="dewasaValue">1</span>
                        <button type="button" class="counter-btn" onclick="updateCounter('dewasa', 1)">+</button>
                    </div>
                </div>
                
                <div class="guest-room-item">
                    <div class="guest-room-label">Anak</div>
                    <div class="counter">
                        <button type="button" class="counter-btn" onclick="updateCounter('anak', -1)" disabled>-</button>
                        <span class="counter-value" id="anakValue">0</span>
                        <button type="button" class="counter-btn" onclick="updateCounter('anak', 1)">+</button>
                    </div>
                </div>
                
                <div class="guest-room-item">
                    <div class="guest-room-label">Kamar</div>
                    <div class="counter">
                        <button type="button" class="counter-btn" onclick="updateCounter('kamar', -1)" disabled>-</button>
                        <span class="counter-value" id="kamarValue">1</span>
                        <button type="button" class="counter-btn" onclick="updateCounter('kamar', 1)">+</button>
                    </div>
                </div>
                </div>
                </div>
        
            <button type="submit" class="search-btn">
            <i class="fa-solid fa-magnifying-glass"></i>Cari Hotel
            </button>
      
            </div>
            </div>
    </form>
</div>




    <br>
            <section class="hotel-favorit">
                <div class="hotel-title">
                 <h4>Hotel Favorit</h4>
                 <p>Berikut beberapa hotel berbitang 5 di website kami:</p>
                </div>
                
               <hr>

                <div class="container">
                <?php foreach ($hotels as $hotel): ?>
                <a href="hotel2.php?id_hotel=<?php echo $hotel['id_hotel']; ?>" class="hotel-link">
                    <div class="hotel-card">
                        <img src="/JAVAST/Admin/Gambar/Hotel/<?php echo $hotel['gambar_hotel']; ?>" alt="<?php echo $hotel['nama_hotel']; ?>" class="hotel-image">
                        <div class="hotel-info">
                            <h5><?php echo $hotel['nama_hotel']; ?></h5>
                            <p class="hotel-location"><?php echo $hotel['lokasi_hotel'] . ', ' . $hotel['kota_hotel']; ?></p>
                            <p class="hotel-address"><?php echo $hotel['alamat_hotel']; ?></p>
                            <div class="hotel-rating">
                                <span><?php echo str_repeat("⭐", $hotel['bintang_hotel']); ?></span>
                            </div>
                            <div class="hotel-price">
                                <span>1 malam</span>
                                <span class="price">Rp. <?php echo number_format($hotel['harga_terendah'], 0, ',', '.'); ?></span>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>


                

                       
            </div>

    </section> 

    <br>
    <br>
    <br>

    <section class="kota-rekomendasi">
        <div class="hotel-title">
            <h4>Rekomendasi kota</h4>
            <p class="text">Perlu ide tujuan? kunjungi kota-kota berikut!</p>
        </div>

        <hr>

    <div class="kota">
            <a href="semarang.php" class="kota-card">
                <div class="overlay"></div>
                <h3 class="nama-kota">Semarang</h3>
                <img src="gambar/gambarkota/Semarang.jpg" alt="Semarang" class="image">
            </a>

    
        <a href="jepara.php" class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Jepara</h3>
            <img src="gambar/gambarkota/Jepara.jpeg" alt="Jepara" class="image">
                </a>


        <a href="salatiga.php" class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Salatiga</h3>
            <img src="gambar/gambarkota/Salatiga.jpg" alt="Salatiga" class="image">
            </a>

        <a href="jogja.php" class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Yogyakarta</h3>
            <img src="gambar/gambarkota/Jogja.jpeg" alt="Yogyakarta" class="image">
            </a>
        

        <a href="purwokerto.php" class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Purwokerto</h3>
            <img src="gambar/gambarKota/Purwokerto.jpeg" alt="Purwokerto" class="image">
            </a>
        

        <a href="surakarta.php" class="kota-card">
            <div class="overlay"></div>
            <h3 class="nama-kota">Surakarta</h3>
            <img src="gambar/gambarkota/Surakarta.jpeg" alt="Surakarta" class="image">
            </a>
        
    </div>

        <!-- <div class="button-container">
            <button class="kota-button" >Lihat Hotel Lainnya >></button>
        </div> -->

    </section>

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


        function updateGuestRoomDisplay() {
            const dewasa = document.getElementById('dewasa').value;
            const anak = document.getElementById('anak').value;
            const kamar = document.getElementById('kamar').value;
            document.getElementById('guestRoomDisplay').textContent = 
                `${dewasa} Dewasa, ${anak} Anak, ${kamar} Kamar`;
        }




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
        
        const locationInput = document.getElementById('locationInput');
        const locationDropdown = document.getElementById('locationDropdown');
        const cityListItems = document.querySelectorAll('.city-list li');
        
        // Tampilkan dropdown ketika input difokuskan
        locationInput.addEventListener('focus', function() {
            locationDropdown.style.display = 'block';
        });
        
        // Sembunyikan dropdown ketika klik di luar
        document.addEventListener('click', function(e) {
            if (e.target !== locationInput) {
                locationDropdown.style.display = 'none';
            }
        });
        
        // Isi input ketika memilih kota dari dropdown
        cityListItems.forEach(item => {
            item.addEventListener('click', function() {
                const city = this.getAttribute('data-city');
                locationInput.value = city;
                locationDropdown.style.display = 'none';
            });
        });


// Fungsi untuk update input hidden
function updateHiddenValues() {
    document.getElementById('hiddenDewasa').value = document.getElementById('dewasaValue').textContent;
    document.getElementById('hiddenAnak').value = document.getElementById('anakValue').textContent;
    document.getElementById('hiddenKamar').value = document.getElementById('kamarValue').textContent;
}

// Fungsi utama untuk update counter
function updateCounter(type, change) {
    const input = document.getElementById(type);
    const display = document.getElementById(type + 'Value');
    const btnMinus = display.previousElementSibling; // Tombol minus
    const btnPlus = display.nextElementSibling; // Tombol plus
    let value = parseInt(display.textContent) + change;

    // Validasi nilai
    if (type === 'dewasa') {
        if (value < 1) return; // Dewasa minimal 1
        if (value > 15) return; // Dewasa maksimal 15 (baru)
    } else if (type === 'kamar') {
        if (value < 1) return; // Kamar minimal 1
    } else if (type === 'anak') {
        if (value < 0) return; // Anak minimal 0
        if (value > 6) return; // Anak maksimal 6
    }

    // Update nilai
    display.textContent = value;
    input.value = value;

    // Logika disable tombol minus
    if ((type === 'dewasa' || type === 'kamar') && value <= 1) {
        btnMinus.disabled = true;
    } else if (type === 'anak' && value <= 0) {
        btnMinus.disabled = true;
    } else {
        btnMinus.disabled = false;
    }

    // Logika disable tombol plus
    if (type === 'dewasa' && value >= 15) {
        btnPlus.disabled = true;
    } else if (type === 'anak' && value >= 6) {
        btnPlus.disabled = true;
    } else {
        btnPlus.disabled = false;
    }

    updateGuestRoomDisplay();
    updateHiddenValues();
}


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

        // Button tambah durasi
increaseDuration.addEventListener('click', function() {
    let duration = parseInt(durationValue.textContent);
    if (duration >= 30) return; // Tambahkan validasi maksimal 30
    duration++;
    durationValue.textContent = duration;
    updateCheckOutDate();
    
    // Nonaktifkan tombol + jika mencapai maksimal
    if (duration >= 30) {
        increaseDuration.disabled = true;
    }
    
    // Aktifkan tombol - jika sebelumnya disabled
    decreaseDuration.disabled = false;
});

// Button kurang durasi
decreaseDuration.addEventListener('click', function() {
    let duration = parseInt(durationValue.textContent);
    if (duration > 1) {
        duration--;
        durationValue.textContent = duration;
        updateCheckOutDate();
        
        // Aktifkan tombol + jika durasi kurang dari 30
        if (duration < 30) {
            increaseDuration.disabled = false;
        }
        
        // Nonaktifkan tombol - jika mencapai minimal
        if (duration <= 1) {
            decreaseDuration.disabled = true;
        }
    }
});
        // Update check-out pas check-in berubah
        checkInDate.addEventListener('change', updateCheckOutDate);
        
        updateCheckOutDate();


    document.addEventListener('DOMContentLoaded', function() {
    // Nonaktifkan tombol minus awal
    document.querySelector('[onclick="updateCounter(\'dewasa\', -1)"]').disabled = true;
    document.querySelector('[onclick="updateCounter(\'kamar\', -1)"]').disabled = true;
    
    // Nonaktifkan tombol plus jika nilai awal sudah maksimal
    const dewasaValue = parseInt(document.getElementById('dewasaValue').textContent);
    const anakValue = parseInt(document.getElementById('anakValue').textContent);
    
    if (dewasaValue >= 15) {
        document.querySelector('[onclick="updateCounter(\'dewasa\', 1)"]').disabled = true;
    }
    if (anakValue >= 6) {
        document.querySelector('[onclick="updateCounter(\'anak\', 1)"]').disabled = true;
    }

    updateHiddenValues();
});

    // Nonaktifkan tombol + jika durasi awal sudah 30
if (parseInt(durationValue.textContent) >= 30) {
    increaseDuration.disabled = true;
}

// Nonaktifkan tombol - jika durasi awal 1
if (parseInt(durationValue.textContent) <= 1) {
    decreaseDuration.disabled = true;
}


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
    </script>

</body>
</html>

