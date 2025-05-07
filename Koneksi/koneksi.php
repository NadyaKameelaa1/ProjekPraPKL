<?php

$server = "localhost";
$username = "root";
$password = "";
$database = "javast03";

$koneksi = mysqli_connect($server, $username, $password, $database);

// if($koneksi){
//     echo "berhasil terkoneksi";
// } else{
//     echo "gagal terkoneksi";
// }

define("UPLOAD_SRC",$_SERVER['DOCUMENT_ROOT']."/JAVAST/Admin/Gambar/Hotel/");


define("FETCH_SRC","http://127.0.0.1/JAVAST/Admin/Gambar/Hotel/");
?>