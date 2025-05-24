<?php
session_start();
require_once '../Koneksi/koneksi.php';

// Tampilkan pesan error/sukses
if (isset($_SESSION['errors'])) {
    echo '<div class="alert alert-danger">';
    foreach ($_SESSION['errors'] as $error) {
        echo '<p>'.$error.'</p>';
    }
    echo '</div>';
    unset($_SESSION['errors']);
}

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
    unset($_SESSION['success']);
}

// Tentukan mode tampilan
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'list';

// Query untuk menampilkan data kamar
$sql = "SELECT k.*, h.nama_hotel 
        FROM kamar k
        JOIN hotels h ON k.id_hotel = h.id_hotel
        ORDER BY k.id_kamar DESC";
$query = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kamar | Javast - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="kamar.css">

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
            <div class="menu-item active" onclick="window.location.href='kamar.php'">
                <i class="fas fa-bed"></i> Kamar
            </div>
            <div class="menu-item" onclick="window.location.href='kontak.php'">
                <i class="fas fa-envelope"></i> Kontak Kami
            </div>
            <div class="menu-item" onclick="window.location.href='booking.php'">
                <i class="fas fa-calendar-check"></i> Booking <i class="fa-solid fa-caret-up"></i>
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
        <h1>KAMAR</h1>
    </div>
    <div class="header-2">
        <h1>Pilihan Kamar</h1>
    </div>

    <div class="table-controls">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="userSearch" placeholder="Cari id kamar, id hotel, nama kamar, tipe kasur, ukuran, fasilitas, harga, jumlah kamar, tamu, deskripsi...">
        </div>

         <button class="btn-add" class="button" onclick="openPopup()">
            <i class="fas fa-plus"></i> Tambah
        </button> 
    </div>
    
    <div class="table-container">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Id Kamar</th>
                    <th>Id Hotel</th>
                    <th>Nama Kamar</th>
                    <th>Tipe Kasur</th>
                    <th>Ukuran</th>
                    <th>Kapasitas</th>
                    <th>Fasilitas</th>
                    <th>Harga</th>
                    <th>Jumlah Kamar</th>
                    <th>Jumlah Dewasa</th>
                    <th>Jumlah Anak</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            while($kamar=mysqli_fetch_assoc($query)){ 
            
            echo<<<kamar
            <td>$kamar[id_kamar]</td>
            <td>$kamar[id_hotel]</td>
            <td>$kamar[nama_kamar]</td>
            <td>$kamar[tipe_kasur]</td>
            <td>$kamar[ukuran_kamar]</td>
            <td>$kamar[kapasitas_kamar]</td>
            <td>$kamar[fasilitas_kamar]</td>
            <td>$kamar[harga_kamar]</td>
            <td>$kamar[jumlah_kamar]</td>
            <td>$kamar[jumlah_dewasa]</td>
            <td>$kamar[jumlah_anak]</td>
            <td>$kamar[deskripsi_kamar]</td>
            <td>
                <button class="btn-edit" onclick="openPopupedit({$kamar['id_kamar']})">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button class="btn-delete">
                    <a href="kamar_hapus.php?id_kamar={$kamar['id_kamar']}">
                        <i class="fas fa-trash"></i>
                    </a>
                </button>
                <button class="btn-detailGambar">
                    <a href="kamar_detail_gambar.php?id_kamar={$kamar['id_kamar']}">
                        <i class="fa-solid fa-image"></i>
                    </a>
                </button>
            </td>
            </tr>
            
            kamar;
            }  ?>
            </tbody>
        </table>
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



  <!-- Popup tambah -->
  <div class="popup-overlay" id="popup">
    <div class="popup-content">
      <div class="popup-header">
        <h2>TAMBAH KAMAR</h2>
        <span class="close-btn" onclick="closePopup()">&times;</span>
      </div>
  
        <?php
        // Tampilkan pesan error jika ada
        if(isset($_SESSION['errors'])) {
            echo '<div class="alert alert-danger">';
            foreach($_SESSION['errors'] as $error) {
                echo '<p>'.$error.'</p>';
            }
            echo '</div>';
            unset($_SESSION['errors']);
        }

        // Tampilkan pesan sukses jika ada
        if(isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
            unset($_SESSION['success']);
        }

        // Isi kembali form dengan data sebelumnya jika ada error
        $form_data = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        ?>

      <div class="form-container">
        <form class="room-form" action="kamar_proses_tambah.php" method="POST">
            <div class="form-group">
                <label for="hotel-id">Id Hotel</label>
                <div class="hotel-selection">
                <select name="id_hotel" id="hotel-id" required>
                        <option value="">Pilih Hotel</option>
                        <?php
                        $hotel = mysqli_query($koneksi, "SELECT id_hotel, nama_hotel FROM hotels");
                        while($hotels = mysqli_fetch_assoc($hotel)) {
                            $selected = ($hotels['id_hotel'] == ($_POST['id_hotel'] ?? '')) ? 'selected' : '';
                            echo '<option value="'.$hotels['id_hotel'].'" '.$selected.'>'.$hotels['nama_hotel'].'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="room-name">Nama Kamar</label>
                <input type="text" name="nama_kamar" id="room-name" value="<?= htmlspecialchars($_POST['nama_kamar'] ?? '') ?>" placeholder="Nama Kamar Hotel..." required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="room-name">Tipe Kasur</label>
                    <input type="text" name="tipe_kasur" id="room-bed" value="<?= htmlspecialchars($_POST['tipe_kasur'] ?? '') ?>" placeholder="Tipe Kasur Hotel..." required>
                </div>

                <div class="form-group">
                    <label for="room-size">Ukuran (m²)</label>
                    <input type="number" name="ukuran_kamar" id="room-size" value="<?= htmlspecialchars($_POST['ukuran_kamar'] ?? '') ?>" placeholder="Ukuran Kamar Hotel..." required>
                </div>
            </div>

            <div class="form-group">
                <label for="capacity">Kapasitas</label>
                <input type="number" name="kapasitas_kamar" id="capacity" value="<?= htmlspecialchars($_POST['kapasitas_kamar'] ?? '') ?>" placeholder="Kapasitas Hotel..." min="1" max="10" required>
            </div>

            <div class="form-group">
                <label for="facilities">Fasilitas</label>
                <textarea id="facilities" name="fasilitas_kamar" value="<?= htmlspecialchars($_POST['fasilitas_kamar'] ?? '') ?>" placeholder="Fasilitas Hotel..." required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Harga</label>
                    <input type="number" name="harga_kamar" id="price" value="<?= htmlspecialchars($_POST['harga_kamar'] ?? '') ?>" placeholder="Harga Hotel..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Jumlah Kamar</label>
                    <input type="number" name="jumlah_kamar" value="<?= htmlspecialchars($_POST['jumlah_kamar'] ?? '') ?>" placeholder="Jumlah Kamar..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Jumlah Dewasa</label>
                    <input type="number" name="jumlah_dewasa" value="<?= htmlspecialchars($_POST['jumlah_dewasa'] ?? '') ?>" placeholder="Jumlah Dewasa..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Jumlah Anak</label>
                    <input type="number" name="jumlah_anak" value="<?= htmlspecialchars($_POST['jumlah_anak'] ?? '') ?>" placeholder="Jumlah Anak..." required>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi Kamar</label>
                <textarea id="description" name="deskripsi_kamar" value="<?= htmlspecialchars($_POST['deskripsi_kamar'] ?? '') ?>" placeholder="Deskripsi Hotel..." required></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closePopup()">Batal</button>
                <button type="submit" name="submit" class="btn-submit">Tambah Kamar</button>
              </div>
        </form>
    </div>
</div>


  <!-- Popup edit -->
  <div class="popup-overlay" id="popupedit">
    <div class="popup-content">
      <div class="popup-header">
        <div>
        <h2>EDIT KAMAR</h2>
        <h3>New Deluxe Twin Room Only</h3>
        </div>
        <span class="close-btn" onclick="closePopupedit()">&times;</span>
      </div>
  
        <form class="room-form">
            <div class="form-row">
            <div class="form-group">
                <label for="hotel-id">Id Hotel</label>
                <div class="hotel-selection">
                    <select id="hotel-id">
                        <option value="201" selected>201 - Semarang</option>
                        <option value="202">202 - Semarang</option>
                        <option value="203">203 - Purwokerto</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="room-name">Nama Kamar</label>
                <input type="text" id="room-name" placeholder="Nama Kamar Hotel...">
            </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="room-name">Tipe Kasur</label>
                    <input type="text" id="room-bed" placeholder="Tipe Kasur Hotel...">
                </div>

                <div class="form-group">
                    <label for="room-size">Ukuran (m²)</label>
                    <input type="text" id="room-size" placeholder="Ukuran Kamar Hotel...">
                </div>
            </div>

            <div class="form-group">
                <label for="capacity">Kapasitas</label>
                <input type="number" id="capacity" placeholder="Kapasitas Hotel..." min="1" max="10">
            </div>

            <div class="form-group">
                <label for="facilities">Fasilitas</label>
                <textarea id="facilities" placeholder="Fasilitas Hotel..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Harga</label>
                    <input type="text" id="price" placeholder="Harga Hotel...">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" placeholder="Deskripsi Hotel..."></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closePopupedit()">Batal</button>
                <button type="submit" class="btn-submit">Tambah Kamar</button>
              </div>
        </form>
    </div>
</div>

    <!-- Popup gambar -->
  <div class="popup-overlay" id="popup-gambar">
    <div class="popup-content">
      <div class="popup-header">
        <h2>TAMBAH KAMAR</h2>
        <span class="close-btn" onclick="closePopupgambar()">&times;</span>
      </div>

      <div class="form-container">
        <form class="room-form" action="kamar_tambah_gambar.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_gambar">
            <div class="form-group">
                <label>Nama Kamar:</label>
                <p class="nama-kamar-display"><?= htmlspecialchars($_GET['nama_kamar'] ?? ' ') ?></p>
                <input type="hidden" name="id_kamar" value="<?= $_GET['id_kamar'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Gambar 1</label>
                <input type="file" name="gambarA" multiple accept=".jpg,.png,.webp,.svg">
            </div>
            <div class="form-group">
                <label>Gambar 2</label>
                <input type="file" name="gambarB" multiple accept=".jpg,.png,.webp,.svg">
            </div>
            <div class="form-group">
                <label>Gambar 3</label>
                <input type="file" name="gambarC" multiple accept=".jpg,.png,.webp,.svg">
            </div>
            <div class="form-group">
                <label>Gambar 4</label>
                <input type="file" name="gambarD" multiple accept=".jpg,.png,.webp,.svg">
            </div>
            <div class="form-group">
                <label>Gambar 5</label>
                <input type="file" name="gambarE" multiple accept=".jpg,.png,.webp,.svg">
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closePopupgambar()">Batal</button>
                <button type="submit" name="upload" class="btn-submit">Simpan Gambar</button>
            </div>
        </form>
    </div>
</div>


<script>
// // Script untuk menampilkan nama file yang dipilih
//         document.getElementById('hotel-image').addEventListener('change', function(e) {
//             const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
//             document.querySelector('.file-chosen').textContent = fileName;
//         });

//         document.getElementById('room-image').addEventListener('change', function(e) {
//             const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
//             document.querySelector('.file-chosen').textContent = fileName;
//         });

//-----------------------------------------------------------------------
    
// tambah
function openPopup() {
  document.getElementById("popup").style.display = "flex";
}

function closePopup() {
  document.getElementById("popup").style.display = "none";
}

// edit
function openPopupedit() {
  document.getElementById("popupedit").style.display = "flex";
}

function closePopupedit() {
  document.getElementById("popupedit").style.display = "none";
}

// Fungsi untuk membuka popup gambar
function openPopupgambar(id_kamar) {
    console.log('Mencoba membuka popup untuk kamar ID:', id_kamar); // Debug 1
    
    fetch(`kamar_get_data.php?id_kamar=${id_kamar}`)
        .then(response => {
            console.log('Response status:', response.status); // Debug 2
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            console.log('Data diterima:', data); // Debug 3
            if (data && data.nama_kamar) {
                document.querySelector('#popup-gambar .nama-kamar-display').textContent = data.nama_kamar;
                document.querySelector('#popup-gambar input[name="id_kamar"]').value = id_kamar;
                document.getElementById("popup-gambar").style.display = "flex";
            } else {
                console.error('Data tidak valid atau nama_kamar tidak ada');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback: Tampilkan popup meski tanpa data
            document.getElementById("popup-gambar").style.display = "flex";
        });
}

// Fungsi penutup
function closePopupgambar() {
    document.getElementById('popup-gambar').style.display = 'none';
}

// Event listener untuk form submit
document.querySelector('.room-form')?.addEventListener('submit', function() {
    setTimeout(closePopupgambar, 1000);
});

//----------------------------------------------------------------

// // Membuka popup dengan data kamar yang dipilih
// function openAddImagePopup(id_kamar, nama_kamar) {
//     const popup = document.getElementById('image-popup');
//     popup.querySelector('.nama-kamar-display').textContent = nama_kamar;
//     popup.querySelector('input[name="id_kamar"]').value = id_kamar;
//     popup.style.display = 'block';
// }

</script>

</body>
</html>