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

// Ambil data user
$email = $_SESSION['email_user'];
$query_user = mysqli_query($koneksi, "SELECT nama_user, no_telp, alamat_user FROM users WHERE email_user = '$email'");
$user = mysqli_fetch_assoc($query_user);

$tanggal1 = new DateTime($check_in);
$tanggal2 = new DateTime($check_out);
$jumlah_hari = $tanggal2->diff($tanggal1)->days;

// Hitung total bayar awal
$total_bayar = $detail_kamar['harga_kamar'] * $jumlah_hari * $jumlah_kamar;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan <?= $hotels['nama_hotel'];?> | Javast</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="pesan.css">
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

    <!-- pesan -->

    <div class="steps">
        <div class="step active">Pesan</div>
        <div class="arrow"><i class="fa-solid fa-right-long"></i></div>
        <div class="step">Bayar</div>
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
        </div>
    
        <div class="order-box">
          <h4><b>Pesanan</b></h4>
            <form action="proses_pesan.php" method="POST">
                <input type="hidden" name="id_hotel" value="<?= $id_hotel ?>">
                <input type="hidden" name="id_kamar" value="<?= $id_kamar ?>">
                <input type="hidden" name="check_in" value="<?= $check_in ?>">
                <input type="hidden" name="check_out" value="<?= $check_out ?>">
              <input type="hidden" id="hiddenDewasa" name="dewasa" value="<?= $dewasa ?>">
              <input type="hidden" id="hiddenAnak" name="anak" value="<?= $anak ?>">
              <input type="hidden" id="hiddenKamar" name="kamar" value="<?= $jumlah_kamar ?>">
              <input type="hidden" id="hiddenTotalBayar" name="total_bayar" value="<?= $total_bayar ?>">
            <div class="input-group">
              <label>Nama</label>
              <input type="text" value="<?= htmlspecialchars($user['nama_user']) ?>" readonly>
              <label>No. Telp</label>
              <input type="text" value="<?= htmlspecialchars($user['no_telp']) ?>" readonly>
            </div>
    
            <label>Alamat</label>
            <input type="text" value="<?= htmlspecialchars($user['alamat_user']) ?>" readonly>

              <div class="guest-room-section">
              <label>Tamu dan Kamar</label>
              <div class="guest-room-container">
                  <div class="guest-room-trigger" id="guestRoomTrigger">
                      <span id="guestRoomDisplay"><?= "$dewasa Dewasa, $anak Anak, $jumlah_kamar Kamar" ?></span>
                      <i class="fas fa-chevron-down"></i>
                  </div>
                  
                  <div class="guest-room-dropdown" id="guestRoomDropdown">
                      <div class="guest-room-item">
                          <div class="guest-room-label"><b>Dewasa</b></div>
                          <div class="counter">
                              <button type="button" class="counter-btn" onclick="updateCounter('dewasa', -1)" <?= $dewasa <= 1 ? 'disabled' : '' ?>>-</button>
                              <span class="counter-value" id="dewasaValue"><?= $dewasa ?></span>
                              <button type="button" class="counter-btn" onclick="updateCounter('dewasa', 1)" <?= $dewasa >= 15 ? 'disabled' : '' ?>>+</button>
                          </div>
                      </div>
                      
                      <div class="guest-room-item">
                          <div class="guest-room-label"><b>Anak</b></div>
                          <div class="counter">
                              <button type="button" class="counter-btn" onclick="updateCounter('anak', -1)" <?= $anak <= 0 ? 'disabled' : '' ?>>-</button>
                              <span class="counter-value" id="anakValue"><?= $anak ?></span>
                              <button type="button" class="counter-btn" onclick="updateCounter('anak', 1)" <?= $anak >= 6 ? 'disabled' : '' ?>>+</button>
                          </div>
                      </div>
                      
                      <div class="guest-room-item">
                          <div class="guest-room-label"><b>Kamar</b></div>
                          <div class="counter">
                              <button type="button" class="counter-btn" onclick="updateCounter('kamar', -1)" <?= $jumlah_kamar <= 1 ? 'disabled' : '' ?>>-</button>
                              <span class="counter-value" id="kamarValue"><?= $jumlah_kamar ?></span>
                              <button type="button" class="counter-btn" onclick="updateCounter('kamar', 1)" <?= $jumlah_kamar >= 10 ? 'disabled' : '' ?>>+</button>
                          </div>
                      </div>
                  </div>
              </div>
              
              <!-- Input hidden untuk form -->
              
          </div>
            <div class="date-group">
              <div class="date-box">
                  <label>Check In</label>
                  <input type="date" id="checkInDate" name="check_in" value="<?= $check_in ?>" class="date-input">
              </div>
              <div class="date-box">
                  <label>Check Out</label>
                  <input type="date" id="checkOutDate" name="check_out" value="<?= $check_out ?>" class="date-input" readonly>
              </div>
            </div>
            
            <br>
            
            
            <div class="date-group">
    <div class="duration-container">
        <div class="duration-box">
            <div class="duration-counter">
                <button type="button" class="duration-btn" id="decreaseDuration">-</button>
                <span class="duration-value" id="durationValue"><?= $jumlah_hari ?></span>
                <button type="button" class="duration-btn" id="increaseDuration">+</button>
               
            </div>
        </div>
    </div>
</div>
            <p class="total">Total Bayar : Rp. <?= number_format($total_bayar, 0, ',', '.') ?></p>
    
           <a href="pembayaran.php?id_hotel=<?= $detail_kamar['id_hotel'] ?>&id_kamar=<?= $detail_kamar['id_kamar'] ?>&check_in=<?= htmlspecialchars($check_in) ?>&check_out=<?= htmlspecialchars($check_out) ?>&dewasa=<?= (int)$dewasa ?>&anak=<?= (int)$anak ?>&kamar=<?= (int)$detail_kamar ?>" ><button type="button">Lanjutkan ke pembayaran</button></a> 
          </form>
          
        </div>
      </div>



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
                    <li><a href="home.html">Beranda</a></li>
                    <li><a href="#">Hotel</a></li>
                    <li><a href="tentang.html">Tentang</a></li>
                    <li><a href="kontak_kami.html">Kontak Kami Us</a></li>
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
    const display = document.getElementById(type + 'Value');
    const hiddenInput = document.getElementById('hidden' + type.charAt(0).toUpperCase() + type.slice(1));
    let value = parseInt(display.textContent) + change;

    // Validasi nilai
    if (type === 'dewasa') {
        if (value < 1) return; // Dewasa minimal 1
        if (value > 15) return; // Dewasa maksimal 15
    } else if (type === 'kamar') {
        if (value < 1) return; // Kamar minimal 1
        if (value > 10) return; // Kamar maksimal 10
    } else if (type === 'anak') {
        if (value < 0) return; // Anak minimal 0
        if (value > 6) return; // Anak maksimal 6
    }

    // Update nilai
    display.textContent = value;
    hiddenInput.value = value;

    // Update tombol minus dan plus
    updateButtonStates(type, value);
    
    // Update tampilan dropdown trigger
    updateGuestRoomDisplay();
    updateHiddenValues();
}

// Fungsi untuk update state tombol
function updateButtonStates(type, value) {
    const minusButton = document.querySelector(`.counter-btn[onclick="updateCounter('${type}', -1)"]`);
    const plusButton = document.querySelector(`.counter-btn[onclick="updateCounter('${type}', 1)"]`);

    // Update tombol minus
    if ((type === 'dewasa' && value <= 1) || 
        (type === 'kamar' && value <= 1) || 
        (type === 'anak' && value <= 0)) {
        minusButton.disabled = true;
    } else {
        minusButton.disabled = false;
    }

    // Update tombol plus
    if ((type === 'dewasa' && value >= 15) || 
        (type === 'anak' && value >= 6) || 
        (type === 'kamar' && value >= 10)) {
        plusButton.disabled = true;
    } else {
        plusButton.disabled = false;
    }
}

// Fungsi untuk update tampilan dropdown trigger
function updateGuestRoomDisplay() {
    document.getElementById('guestRoomDisplay').textContent = 
        `${document.getElementById('dewasaValue').textContent} Dewasa, ${document.getElementById('anakValue').textContent} Anak, ${document.getElementById('kamarValue').textContent} Kamar`;
}

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Nonaktifkan tombol minus awal jika nilai minimal
    const initialValues = {
        'dewasa': parseInt(document.getElementById('dewasaValue').textContent),
        'anak': parseInt(document.getElementById('anakValue').textContent),
        'kamar': parseInt(document.getElementById('kamarValue').textContent)
    };

    for (const type in initialValues) {
        updateButtonStates(type, initialValues[type]);
    }

    updateHiddenValues();
});

// document.addEventListener("DOMContentLoaded", function() {
//         const locationInput = document.getElementById("locationInput");
//         const locationDropdown = document.getElementById("locationDropdown");
//         const cityListItems = document.querySelectorAll(".city-list li");

//       // Tampilkan/ sembunyikan dropdown saat klik input
//     locationInput.addEventListener("click", function(e) {
//         e.stopPropagation();
//         locationDropdown.style.display = locationDropdown.style.display === "block" ? "none" : "block";
//     });

//       // Klik kota akan isi input dan tutup dropdown
//     cityListItems.forEach(function(li) {
//         li.addEventListener("click", function() {
//           locationInput.value = li.getAttribute("data-city");
//           locationDropdown.style.display = "none";
//       });
//     });

//       // Klik di luar input & dropdown, tutup dropdown
//     document.addEventListener("click", function(event) {
//         if (!locationInput.contains(event.target) && !locationDropdown.contains(event.target)) {
//           locationDropdown.style.display = "none";
//         }
//       });
//     });
    
     // Date and Duration Functionality
        // const checkInDate = document.getElementById('checkInDate');
        // const checkOutDate = document.getElementById('checkOutDate');
        // const increaseDuration = document.getElementById('increaseDuration');
        // const decreaseDuration = document.getElementById('decreaseDuration');
        // const durationValue = document.getElementById('durationValue');

        // // Set default dates
        // const today = new Date();
        // const tomorrow = new Date();
        // tomorrow.setDate(today.getDate() + 1);
        
        // checkInDate.valueAsDate = today;
        // checkOutDate.valueAsDate = tomorrow;

        // // Format tampilan tanggal (YYYY-MM-DD)
        // function formatDate(date) {
        //     const year = date.getFullYear();
        //     const month = String(date.getMonth() + 1).padStart(2, '0');
        //     const day = String(date.getDate()).padStart(2, '0');
        //     return `${year}-${month}-${day}`;
        // }

        // // mengubah otomatis checkout
        // function updateCheckOutDate() {
        //     if (!checkInDate.value) return;
            
        //     const duration = parseInt(durationValue.textContent);
        //     const startDate = new Date(checkInDate.value);
        //     const endDate = new Date(startDate);
        //     endDate.setDate(startDate.getDate() + duration);
            
        //     checkOutDate.value = formatDate(endDate);
        // }

        // Button tambah durasi
// increaseDuration.addEventListener('click', function() {
//     let duration = parseInt(durationValue.textContent);
//     if (duration >= 30) return; // Tambahkan validasi maksimal 30
//     duration++;
//     durationValue.textContent = duration;
//     updateCheckOutDate();
    
//     // Nonaktifkan tombol + jika mencapai maksimal
//     if (duration >= 30) {
//         increaseDuration.disabled = true;
//     }
    
//     // Aktifkan tombol - jika sebelumnya disabled
//     decreaseDuration.disabled = false;
// });

// // Button kurang durasi
// decreaseDuration.addEventListener('click', function() {
//     let duration = parseInt(durationValue.textContent);
//     if (duration > 1) {
//         duration--;
//         durationValue.textContent = duration;
//         updateCheckOutDate();
        
//         // Aktifkan tombol + jika durasi kurang dari 30
//         if (duration < 30) {
//             increaseDuration.disabled = false;
//         }
        
//         // Nonaktifkan tombol - jika mencapai minimal
//         if (duration <= 1) {
//             decreaseDuration.disabled = true;
//         }
//     }
// });
        // Update check-out pas check-in berubah
//         checkInDate.addEventListener('change', updateCheckOutDate);
        
//         updateCheckOutDate();

//     // Nonaktifkan tombol + jika durasi awal sudah 30
// if (parseInt(durationValue.textContent) >= 30) {
//     increaseDuration.disabled = true;
// }

// // Nonaktifkan tombol - jika durasi awal 1
// if (parseInt(durationValue.textContent) <= 1) {
//     decreaseDuration.disabled = true;
// } 



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