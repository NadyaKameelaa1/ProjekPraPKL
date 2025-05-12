<?php
session_start();
require_once '../koneksi/koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edithotel'])) {
    // Ambil data dari form
    $id_hotel = (int)$_POST['id_hotel'];
    $kota_hotel = mysqli_real_escape_string($koneksi, $_POST['kota_hotel']);
    $nama_hotel = mysqli_real_escape_string($koneksi, $_POST['nama_hotel']);
    $bintang_hotel = (int)$_POST['bintang_hotel'];
    $lokasi_hotel = mysqli_real_escape_string($koneksi, $_POST['lokasi_hotel']);
    $alamat_hotel = mysqli_real_escape_string($koneksi, $_POST['alamat_hotel']);
    $fasilitas_hotel = mysqli_real_escape_string($koneksi, $_POST['fasilitas_hotel']);

    // Handle upload gambar jika ada
    $gambar_hotel = null;
    if(isset($_FILES['gambar_hotel']) && $_FILES['gambar_hotel']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['gambar_hotel'];
        
        // Validasi file
        $allowed = ['jpg', 'jpeg', 'png', 'svg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            // Generate nama file unik
            $gambar_hotel = uniqid() . '.' . $ext;
            $upload_path = '' . $gambar_hotel;
            
            if(move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Hapus gambar lama jika ada
                $old_img = mysqli_query($koneksi, "SELECT gambar_hotel FROM hotels WHERE id_hotel = $id_hotel");
                if($old_img && $old_img = mysqli_fetch_assoc($old_img)) {
                    if(file_exists(' /JAVAST/Admin/Gambar/Hotel/' . $old_img['gambar_hotel'])) {
                        unlink(' /JAVAST/Admin/Gambar/Hotel/' . $old_img['gambar_hotel']);
                    }
                }
            } else {
                $_SESSION['error'] = "Gagal mengupload gambar";
                header("Location: hotels.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Format file tidak didukung";
            header("Location: hotels.php");
            exit;
        }
    }

    // Query update
    if($gambar_hotel) {
        $query = "UPDATE hotels SET 
                  kota_hotel = '$kota_hotel',
                  nama_hotel = '$nama_hotel',
                  bintang_hotel = '$bintang_hotel',
                  lokasi_hotel = '$lokasi_hotel',
                  alamat_hotel = '$alamat_hotel',
                  fasilitas_hotel = '$fasilitas_hotel',
                  gambar_hotel = '$gambar_hotel'
                  WHERE id_hotel = $id_hotel";
    } else {
        $query = "UPDATE hotels SET 
                  kota_hotel = '$kota_hotel',
                  nama_hotel = '$nama_hotel',
                  bintang_hotel = '$bintang_hotel',
                  lokasi_hotel = '$lokasi_hotel',
                  alamat_hotel = '$alamat_hotel',
                  fasilitas_hotel = '$fasilitas_hotel'
                  WHERE id_hotel = $id_hotel";
    }

    if(mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Data hotel berhasil diperbarui";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data: " . mysqli_error($koneksi);
    }

    header("Location: hotels.php?update=berhasil");
    exit;
}
?>