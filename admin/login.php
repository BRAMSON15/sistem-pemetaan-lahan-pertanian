<?php
session_start();
if(isset($_SESSION['status']) && $_SESSION['status'] == "login"){
    header("location:dashboard.php");
    exit();
}
require_once '../koneksi.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $data = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $cek = mysqli_num_rows($data);

    if($cek > 0){
        $row = mysqli_fetch_assoc($data);
        if(password_verify($password, $row['password'])){
            $_SESSION['username'] = $username;
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            $_SESSION['role'] = $row['role']; // Save role to session
            $_SESSION['status'] = "login";
            header("location:dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    }else{
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Administrator - SIPETA-GIS</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/template/assets/bootstrap/css/bootstrap.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/template/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/login.css">

</head>
<body>

    <div class="login-card">
        <div class="login-logo">
            <i class="fa fa-leaf"></i>
        </div>
        <h2 class="login-title">
            SISTEM INFORMASI<br>
            PEMETAAN LAHAN PERTANIAN<br>
            BERBASIS GIS
        </h2>
        
        <?php if($error != "") echo "<div class='alert alert-danger'>$error</div>"; ?>
        
        <form action="" method="post">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                </div>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                </div>
                <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Password" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-left: none;">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" name="submit" class="btn btn-login">
                <i class="fa fa-lock"></i> LOGIN
            </button>
        </form>
        <a href="../index.php" class="btn-home">
            <i class="fa fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>

    <script>
        // Toggle Password Visibility
        const togglePasswordBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');

        togglePasswordBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Toggle tipe input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            const icon = this.querySelector('i');
            if(type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>

</body>
</html>
