<?php
session_start(); // HARUS di line paling awal sebelum HTML
$errors = $_SESSION['errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];

// Hapus session setelah digunakan
unset($_SESSION['errors']);
unset($_SESSION['old_input']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Daftar | Javast</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="daftar.css">

    <style>
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
        .error-input {
            border: 1px solid red !important;
        }
    </style>

</head>
<body>

    <div class="container">
        <img src="Logo/Logo_Javast.png" alt="Logo Javast" class="logo">
        <h2>Daftar</h2>
        <p>Anda perlu login/daftar terlebih dahulu untuk mengakses website kami.</p>

        <hr>
        <br>

        <form action="proses_daftar.php" method="post">
        <div class="input-group">
        <label><i class="fas fa-user"></i> Nama Lengkap</label>
        <input 
                type="text" 
                name="nama_user" 
                placeholder="Tulis nama lengkapmu..."
                value="<?= htmlspecialchars($old_input['nama_user'] ?? '') ?>"
                class="<?= isset($errors['nama']) ? 'error-input' : '' ?>"
                required
            >
            <?php if(isset($errors['nama'])): ?>
                <div class="error-message"><?= $errors['nama'] ?></div>
            <?php endif; ?>
        </div>
    
        <div class="input-group">
            <label><i class="fas fa-envelope"></i> Email</label>
            <input type="email" name="email_user" placeholder="Tulis emailmu..." required>
        </div>

        <div class="input-group">
            <label><i class="fas fa-home"></i> Alamat</label>
            <textarea name="alamat_user" id="alamat_user" placeholder="Tulis alamat lengkapmu..." required></textarea>
        </div>
    
        <div class="input-group">
        <label><i class="fas fa-phone"></i> Nomor Telepon</label>
        <input 
                type="text" 
                name="no_telp" 
                placeholder="Tulis nomor telepon aktifmu..."
                value="<?= htmlspecialchars($old_input['no_telp'] ?? '') ?>"
                class="<?= isset($errors['telepon']) ? 'error-input' : '' ?>"
                required
            >
            <?php if(isset($errors['telepon'])): ?>
                <div class="error-message"><?= $errors['telepon'] ?></div>
            <?php endif; ?>
        </div>
     
        <div class="input-group">
            <label><i class="fas fa-lock"></i> Password</label>
            <input 
                type="password" 
                name="password_user" 
                placeholder="Tulis passwordmu..."
                class="<?= isset($errors['password']) ? 'error-input' : '' ?>"
                required
            >
            <?php if(isset($errors['password'])): ?>
                <div class="error-message"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>
        

        <button type="submit" class="button">Daftar!</button>
        </form>
        <br>
        <br>
        <hr>
        <p class="daftar-link">Sudah mendaftar?</p>
        <button class="button-login" onclick="window.location.href='login.php'">Login</button>
    </div>
    
</body>
</html>