<?php
session_start();
require_once '../Koneksi/koneksi.php';

if (!isset($_SESSION['email_user'])) {
    header("Location: login.php");
    exit;
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
            <div class="menu-item" onclick="window.location.href='statistik.php'">
                <i class="fa-solid fa-chart-simple"></i> Statistik
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
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>  

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
            <tr>
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
                    <button class="btn-edit">
                            <a href="kamar_edit.php?id_kamar={$kamar['id_kamar']}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
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

  <!-- Popup tambah -->
  <div class="popup-overlay" id="popup">
    <div class="popup-content">
      <div class="popup-header">
        <h2>TAMBAH KAMAR</h2>
        <span class="close-btn" onclick="closePopup()">&times;</span>
      </div>


      <div class="form-container">
        <form action="kamar_proses_tambah.php" method="POST" class="room-form" enctype="multipart/form-data">
            <div class="form-group">
                <label>Id Hotel</label>
                <div class="hotel-selection">
                <select name="id_hotel" required>
                        <option>Pilih Hotel</option>
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
                <label>Nama Kamar</label>
                <input type="text" name="nama_kamar" placeholder="Nama Kamar..." required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tipe Kasur</label>
                    <input type="text" name="tipe_kasur" placeholder="Tipe Kasur Kamar..." required>
                </div>

            <div class="form-group">
                    <label>Ukuran (m²)</label>
                    <input type="number" name="ukuran_kamar" placeholder="Ukuran Kamar Kamar..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas_kamar" placeholder="Kapasitas Kamar..." min="1" max="10" required>
            </div>

            <div class="form-group">
                <label>Fasilitas</label>
                <textarea name="fasilitas_kamar" placeholder="Fasilitas Kamar..." required></textarea>
            </div>
 
            <div class="form-row">
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga_kamar" placeholder="Harga Kamar..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Kamar</label>
                    <input type="number" name="jumlah_kamar" placeholder="Jumlah Kamar..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Dewasa</label>
                    <input type="number" name="jumlah_dewasa" placeholder="Jumlah Kapasitas Maksimal untuk Orang Dewasa..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Anak</label>
                    <input type="number" name="jumlah_anak" placeholder="Jumlah Kapasitas Maksimal untuk Anak-anak..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi Kamar</label>
                <textarea name="deskripsi_kamar" placeholder="Deskripsi Kamar..." required></textarea>
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
  
        <form id="editKamarForm" action="kamar_proses_edit.php" class="room-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_kamar" id="kamarIdInput" value="">
        <input type="hidden" name="editkamar" value="1">

            <div class="form-row">

            <div class="form-group">
                <label>Nama Kamar</label>
                <input type="text" name="nama_kamar" id="editnama" value="<?= htmlspecialchars($hotels['nama_kamar'] ?? '') ?>" placeholder="Nama Kamar Hotel..." required>
            </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tipe Kasur</label>
                    <input type="text" name="tipe_kasur" id="edittipekasur" value="<?= htmlspecialchars($hotels['tipe_kasur'] ?? '') ?>" placeholder="Tipe Kasur Kamar..." required>
                </div>

                <div class="form-group">
                    <label>Ukuran (m²)</label>
                    <input type="number" name="ukuran_kamar" id="editukuran" value="<?= htmlspecialchars($hotels['ukuran_kamar'] ?? '') ?>" placeholder="Ukuran Kamar Kamar..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas_kamar" id="editkapasitas" value="<?= htmlspecialchars($hotels['kapasitas_kamar'] ?? '') ?>" placeholder="Kapasitas Kamar..." min="1" max="10" required>
            </div>

            <div class="form-group">
                <label>Fasilitas</label>
                <textarea name="fasilitas_kamar" id="editfasilitas" value="<?= htmlspecialchars($hotels['fasilitas_kamar'] ?? '') ?>" placeholder="Fasilitas Kamar..." required></textarea>
            </div>
 
            <div class="form-row">
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga_kamar" id="editharga" value="<?= htmlspecialchars($hotels['harga_kamar'] ?? '') ?>" placeholder="Harga Kamar..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Kamar</label>
                    <input type="number" name="jumlah_kamar" id="editjumlahkamar" value="<?= htmlspecialchars($hotels['jumlah_kamar'] ?? '') ?>" placeholder="Jumlah Kamar..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Dewasa</label>
                    <input type="number" name="jumlah_dewasa" id="editjumlahdewasa" value="<?= htmlspecialchars($hotels['jumlah_dewasa'] ?? '') ?>" placeholder="Jumlah Kapasitas Maksimal untuk Orang Dewasa..." required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jumlah Anak</label>
                    <input type="number" name="jumlah_anak" id="editjumlahanak" value="<?= htmlspecialchars($hotels['jumlah_anak'] ?? '') ?>" placeholder="Jumlah Kapasitas Maksimal untuk Anak-anak..." required>
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi Kamar</label>
                <textarea name="deskripsi_kamar" id="editdeskripsi" value="<?= htmlspecialchars($hotels['deskripsi_kamar'] ?? '') ?>" placeholder="Deskripsi Kamar..." required></textarea>
            </div>


            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closePopupedit()">Batal</button>
                <button type="submit" class="btn-submit" id="submit">Simpan Perubahan</button>
              </div>
        </form>
    </div>
</div>

    <!-- Popup gambar
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
                <p class="nama-kamar-display"> htmlspecialchars($_GET['nama_kamar'] ?? ' ') ?></p>
                <input type="hidden" name="id_kamar" value="< $_GET['id_kamar'] ?? '' ?>">
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
    </div> -->
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
    
// // tambah
// function openPopup() {
//   document.getElementById("popup").style.display = "flex";
// }

// function closePopup() {
//   document.getElementById("popup").style.display = "none";
// }

// // edit
// function openPopupedit() {
//   document.getElementById("popupedit").style.display = "flex";
// }

// function closePopupedit() {
//   document.getElementById("popupedit").style.display = "none";
// }

// // Fungsi untuk membuka popup gambar
// function openPopupgambar(id_kamar) {
//     console.log('Mencoba membuka popup untuk kamar ID:', id_kamar); // Debug 1
    
//     fetch(`kamar_get_data.php?id_kamar=${id_kamar}`)
//         .then(response => {
//             console.log('Response status:', response.status); // Debug 2
//             if (!response.ok) throw new Error('Network response was not ok');
//             return response.json();
//         })
//         .then(data => {
//             console.log('Data diterima:', data); // Debug 3
//             if (data && data.nama_kamar) {
//                 document.querySelector('#popup-gambar .nama-kamar-display').textContent = data.nama_kamar;
//                 document.querySelector('#popup-gambar input[name="id_kamar"]').value = id_kamar;
//                 document.getElementById("popup-gambar").style.display = "flex";
//             } else {
//                 console.error('Data tidak valid atau nama_kamar tidak ada');
//             }
//         })
//         .catch(error => {
//             console.error('Error:', error);
//             // Fallback: Tampilkan popup meski tanpa data
//             document.getElementById("popup-gambar").style.display = "flex";
//         });
// }

// // Fungsi penutup
// function closePopupgambar() {
//     document.getElementById('popup-gambar').style.display = 'none';
// }

// // Event listener untuk form submit
// document.querySelector('.room-form')?.addEventListener('submit', function() {
//     setTimeout(closePopupgambar, 1000);
// });

//----------------------------------------------------------------

// // Membuka popup dengan data kamar yang dipilih
// function openAddImagePopup(id_kamar, nama_kamar) {
//     const popup = document.getElementById('image-popup');
//     popup.querySelector('.nama-kamar-display').textContent = nama_kamar;
//     popup.querySelector('input[name="id_kamar"]').value = id_kamar;
//     popup.style.display = 'block';
// }



    document.getElementById('userSearch').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.crud-table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
 
    // memunculkan popup tambah
    function openPopup() {
      document.getElementById("popup").style.display = "flex";
    }
    
    function closePopup() {
      document.getElementById("popup").style.display = "none";
    }

    // memunculkan popup edit dengan data hotel
function openPopupedit(id_kamar) {
    console.log('Mengedit kamar ID:', id_kamar);
    
    // Pastikan ini mengisi nilai form
    document.getElementById('kamarIdInput').value = id_kamar;
    
    fetch('kamar_data.php?id_kamar=' + id_kamar)
    .then(response => {
        if(!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            // Isi form dengan data yang diterima
            document.getElementById('editnama').value = data.nama_kamar;
            document.getElementById('edittipekasur').value = data.tipe_kasur;
            document.getElementById('editukuran').value = data.ukuran_kamar;
            document.getElementById('editkapasitas').value = data.kapasitas_kamar;
            document.getElementById('editfasilitas').value = data.fasilitas_kamar;
            document.getElementById('editharga').value = data.harga_kamar;
            document.getElementById('editjumlahkamar').value = data.jumlah_kamar;
            document.getElementById('editjumlahdewasa').value = data.jumlah_dewasa;
            document.getElementById('editjumlahanak').value = data.jumlah_anak;
            document.getElementById('editdeskripsi').value = data.deskripsi_kamar;

            // Tambahkan input hidden untuk id_kamar
            const form = document.querySelector('.room-form');
            let idInput = form.querySelector('input[name="id_kamar"]');
            if(!idInput) {
                idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id_kamar';
                form.prepend(idInput);
            }
            idInput.value = id_kamar;

            // Tampilkan nama hotel di header popup
            document.querySelector('#popupedit h3').textContent = data.nama_kamar;
            
            // Tampilkan popup
            document.getElementById("popupedit").style.display = "flex";
        } else {
            alert(data.error || 'Gagal memuat data kamar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memuat data: ' + error.message);
    });
}

function closePopupedit() {
    document.getElementById("popupedit").style.display = "none";
}

document.getElementById('editKamarForm').addEventListener('submit', function(e) {
    console.log('Form submitted!'); 
});

document.getElementById('kamarEditForm').addEventListener('submit', function(e) {
    console.log('Form submit diproses...');
});


</script>

</body>
</html>