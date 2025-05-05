<?php
session_start();
require_once '../Koneksi/koneksi.php';

$kota_hotel = $_GET['kota_hotel'];
$nama_hotel = $_GET['nama_hotel'];
$bintang_hotel = $_GET['bintang_hotel'];
$lokasi_hotel = $_GET['lokasi_hotel'];
$alamat_hotel = $_GET['alamat_hotel'];
$fasilitas_hotel = $_GET['fasilitas_hotel'];
$gambar_hotel = $_GET['gambar_hotel'];

$sql = "INSERT INTO hotels (kota_hotel,nama_hotel,bintang_hotel,lokasi_hotel,alamat_hotel,fasilitas_hotel,gambar_hotel) VALUES ('$kota_hotel','$nama_hotel','$bintang_hotel','$lokasi_hotel','$alamat_hotel','$fasilitas_hotel','$gambar_hotel')";
$query = mysqli_query($koneksi,$sql);

if($query){
    header("location:hotels.php?tambah=sukses");
    exit;
} else{
    header("location:hotels.php?tambah=gagal");
    exit;
}

?>