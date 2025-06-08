<?php
session_start();
require_once '../koneksi/koneksi.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editKamar'])) {
    // Check if id_kamar is set and valid
    if(!isset($_POST['id_kamar']) || empty($_POST['id_kamar'])) {
        $_SESSION['error'] = "ID Kamar tidak valid";
        header("Location: kamar.php");
        exit;
    }

    $id_kamar = (int)$_POST['id_kamar'];
    $nama_kamar = mysqli_real_escape_string($koneksi, $_POST['nama_kamar']);
    $tipe_kasur = mysqli_real_escape_string($koneksi, $_POST['tipe_kasur']);
    $ukuran_kamar = (float)$_POST['ukuran_kamar'];
    $kapasitas_kamar = (int)$_POST['kapasitas_kamar'];
    $fasilitas_kamar = mysqli_real_escape_string($koneksi, $_POST['fasilitas_kamar']);
    $harga_kamar = (int)$_POST['harga_kamar'];
    $jumlah_kamar = (int)$_POST['jumlah_kamar'];
    $jumlah_dewasa = (int)$_POST['jumlah_dewasa'];
    $jumlah_anak = (int)$_POST['jumlah_anak'];
    $deskripsi_kamar = mysqli_real_escape_string($koneksi, $_POST['deskripsi_kamar']);

    $sql = "UPDATE kamar SET
            nama_kamar = '$nama_kamar',
            tipe_kasur = '$tipe_kasur',
            ukuran_kamar = '$ukuran_kamar',
            kapasitas_kamar = '$kapasitas_kamar',
            fasilitas_kamar = '$fasilitas_kamar',
            harga_kamar = '$harga_kamar',
            jumlah_kamar = '$jumlah_kamar',
            jumlah_dewasa = '$jumlah_dewasa',
            jumlah_anak = '$jumlah_anak',
            deskripsi_kamar = '$deskripsi_kamar'
            WHERE id_kamar = $id_kamar";
    
    $query = mysqli_query($koneksi, $sql);

    if (!$query) {
        $_SESSION['error'] = "Gagal memperbarui data: ". mysqli_error($koneksi);
        header("Location: kamar.php?update=gagal");
        exit;
    } else {
        $_SESSION['success'] = "Data kamar berhasil diperbarui";
        header("Location: kamar.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Permintaan tidak valid";
    header("Location: kamar.php");
    exit;
}
?>