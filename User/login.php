<?php
session_start(); // Tetap perlu untuk session error
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Login | Javast</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>


    <div class="container">
        <img src="Logo/Logo_Javast.png" alt="Logo Javast" class="logo">
        <h2>Login</h2>
        <p>Anda perlu login/daftar terlebih dahulu untuk mengakses website kami.</p>
        <br>
        <hr>
        <br>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message" style="color: red;">
                <i class="fas fa-exclamation-circle"></i> 
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?> 

        <form action="proses_login.php" method="POST">
        <div class="input-group">
            <label><i class="fas fa-envelope"></i> Email</label>
            <input type="email" name="email_user" placeholder="Tulis emailmu..." required>
        </div>

        <div class="input-group">
            <label><i class="fas fa-lock"></i> Password</label>
            <input type="password" name="password_user" placeholder="Tulis passwordmu..." required>
        </div>
    
        <button class="button">Login!</button>
        </form>
        <br>
        <hr>
        <p class="daftar-link">Belum punya akun?</p>
        <button class="button-login" onclick="window.location.href='daftar.php'">Daftar</button>
    </div>
    
</body>
</html>