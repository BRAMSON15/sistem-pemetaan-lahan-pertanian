<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit();
}

// Role-based authorization
$current_page = basename($_SERVER['PHP_SELF']);
$petugas_allowed_pages = ['dashboard.php', 'lahan.php', 'logout.php', 'layout_header.php', 'layout_footer.php'];

if(isset($_SESSION['role']) && $_SESSION['role'] == 'petugas'){
    if(!in_array($current_page, $petugas_allowed_pages)){
        // Redirect petugas ke dashboard jika mengakses halaman admin
        header("location:dashboard.php");
        exit();
    }
} else if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
    if($current_page == 'lahan.php'){
        // Redirect admin ke dashboard jika mencoba mengakses Data Lahan
        header("location:dashboard.php");
        exit();
    }
}
?>
