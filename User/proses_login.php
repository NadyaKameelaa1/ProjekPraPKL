<?php
session_start();
require_once '../Koneksi/koneksi.php';

$email = mysqli_real_escape_string($koneksi, $_POST['email_user']);
$password = $_POST['password_user'];

// 1. Cari user berdasarkan email
$sql = "SELECT * FROM users WHERE email_user = '$email'";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: login.php?error=Email tidak terdaftar");
    exit;
}

// 2. Ambil data user
$user = mysqli_fetch_assoc($result);

// 3. Bandingkan password (versi debug)
$input_hash = md5($password);
$db_hash = $user['password_user'];

echo "Input Hash: $input_hash<br>";
echo "DB Hash: $db_hash<br>";

if ($input_hash === $db_hash) {
    $_SESSION['email_user'] = $user['email_user'];
    $_SESSION['name_user'] = $user['name_user'];
    header("Location: home.php");
    exit;
} else {

    // Jika masih gagal, coba metode alternatif
    if ($password === $user['password_user']) { // Jika ternyata password tidak di-hash
        $_SESSION['email_user'] = $user['email_user'];
        $_SESSION['name_user'] = $user['name_user'];
        header("Location: home.php");
        exit;
    } else {
        header("Location: login.php?error=Password salah!");
        exit;
    }

}



// session_start();
// include 'koneksi.php';

// // Debug 1: Tampilkan data yang diterima
// echo "<pre>POST Data: ";
// print_r($_POST);
// echo "</pre>";

// $email = mysqli_real_escape_string($koneksi, $_POST['email_user']);
// $password = $_POST['password_user'];

// // Debug 2: Tampilkan password yang diinput dan versi MD5-nya
// echo "Password Input: ".$password."<br>";
// echo "MD5 Hash: ".md5($password)."<br>";

// // Query database
// $sql = "SELECT * FROM users WHERE email_user = '$email'";
// $result = mysqli_query($koneksi, $sql);

// // Debug 3: Tampilkan query dan hasilnya
// echo "SQL Query: ".$sql."<br>";
// echo "Jumlah Hasil: ".mysqli_num_rows($result)."<br>";

// if (mysqli_num_rows($result) > 0) {
//     $user = mysqli_fetch_assoc($result);
    
//     // Debug 4: Tampilkan data user dari database
//     echo "<pre>User Data: ";
//     print_r($user);
//     echo "</pre>";
    
//     // Bandingkan password
//     if (md5($password) == $user['password_user']) {
//         $_SESSION['email_user'] = $user['email_user'];
//         $_SESSION['name_user'] = $user['name_user'];
        
//         // Debug 5: Tampilkan session yang diset
//         echo "<pre>Session Data: ";
//         print_r($_SESSION);
//         echo "</pre>";
        
//         header("Location: index.php");
//         exit;
//     } else {
//         echo "Debug 6: Password tidak match!";
//         // header("Location: login.php?error=Password salah");
//         // exit;
//     }
// } else {
//     echo "Debug 7: Email tidak ditemukan!";
//     // header("Location: login.php?error=Email tidak terdaftar");
//     // exit;
// }


// session_start();
// include 'koneksi.php';

// $email_user = $_POST['email_user'];
// $password_user = $_POST['password_user'];


// Hindari SQL Injection (gunakan mysqli_real_escape_string atau prepared statement)
// $email_user = mysqli_real_escape_string($koneksi, $email_user);
// $password_user = mysqli_real_escape_string($koneksi, $password_user);

// Hash password dengan MD5 (tidak direkomendasikan untuk produksi, gunakan password_hash())
// $sql = "SELECT * FROM users WHERE email_user = '$email_user' AND password_user = md5('$password_user')";
// $query = mysqli_query($koneksi, $sql);

// if (mysqli_num_rows($query) > 0) {
//     $data = mysqli_fetch_assoc($query);
//     $_SESSION['name_user'] = $data['name_user']; // Simpan nama user ke session
//     header("location: index.php?login=sukses");
//     exit;
// } else {
//     // Cek apakah email ada di database
//     $sql_check_email = "SELECT * FROM users WHERE email_user = '$email_user'";
//     $query_check_email = mysqli_query($koneksi, $sql_check_email);

//     if (mysqli_num_rows($query_check_email)) {
//         // Email ada, tapi password salah
//         header("location: login.php?error=Password salah!");
//     } else {
//         // Email tidak terdaftar
//         header("location: login.php?error=Email belum terdaftar!");
//     }
//     exit;
// }


?>