<?php
require_once 'cek_login.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPETA-GIS - Administrator</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/template/assets/bootstrap/css/bootstrap.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/template/assets/fonts/font-awesome.min.css">
    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
    <!-- Leaflet Fullscreen CSS -->
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
    <link rel="stylesheet" href="../assets/css/layout_header.css">
</head>
<body>

<div class="wrapper">
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fa fa-leaf"></i> SIPETA-GIS</h3>
            <small>Sistem Informasi Pemetaan Lahan Pertanian</small>
        </div>
        <ul class="nav-menu">
            <li class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
            </li>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'petugas'): ?>
            <li class="<?= $current_page == 'lahan.php' ? 'active' : '' ?>">
                <a href="lahan.php"><i class="fa fa-map-marker"></i> Data Lahan</a>
            </li>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <li class="<?= $current_page == 'komoditas.php' ? 'active' : '' ?>">
                <a href="komoditas.php"><i class="fa fa-leaf"></i> Data Tanaman / Komoditas</a>
            </li>
            <!-- Dummy menus to match mockup -->
            <li class="<?= $current_page == 'kecamatan.php' ? 'active' : '' ?>"><a href="kecamatan.php"><i class="fa fa-map"></i> Data Kecamatan</a></li>
            <li class="<?= $current_page == 'kabupaten.php' ? 'active' : '' ?>"><a href="kabupaten.php"><i class="fa fa-map-o"></i> Data Kabupaten</a></li>
            <li class="<?= $current_page == 'provinsi.php' ? 'active' : '' ?>"><a href="provinsi.php"><i class="fa fa-globe"></i> Data Provinsi</a></li>
            <li class="<?= $current_page == 'user.php' ? 'active' : '' ?>"><a href="user.php"><i class="fa fa-users"></i> Data User</a></li>
            <li class="<?= $current_page == 'laporan.php' ? 'active' : '' ?>"><a href="laporan.php"><i class="fa fa-file-text"></i> Laporan</a></li>
            <li class="<?= $current_page == 'pengaturan.php' ? 'active' : '' ?>"><a href="pengaturan.php"><i class="fa fa-cogs"></i> Pengaturan</a></li>
            <?php endif; ?>
            
            <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div>
                <a href="#" id="sidebarToggle" style="color: #333; font-size: 20px;"><i class="fa fa-bars"></i></a>
            </div>
            <div class="topbar-right">
                <span><?= $_SESSION['nama_lengkap'] ?? 'Administrator' ?></span>
                <i class="fa fa-user-circle fa-2x text-secondary"></i>
            </div>
        </header>

        <!-- Page Content Area -->
        <div class="content-area">
