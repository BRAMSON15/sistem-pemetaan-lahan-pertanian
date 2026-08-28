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
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            overflow-x: hidden;
        }
        .wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
        }
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #1a2226;
            color: #b8c7ce;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
            flex-shrink: 0;
            overflow-y: auto;
        }
        .sidebar-header {
            background-color: #1a2226;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #1a2226;
        }
        .sidebar-header h3 {
            color: white;
            font-size: 18px;
            margin: 0;
            font-weight: 700;
        }
        .sidebar-header h3 i {
            color: #4caf50;
            margin-right: 5px;
        }
        .sidebar-header small {
            font-size: 11px;
            display: block;
            margin-top: 5px;
        }
        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .nav-menu li {
            padding: 0;
        }
        .nav-menu li a {
            display: block;
            padding: 12px 20px;
            color: #b8c7ce;
            text-decoration: none;
            font-size: 14px;
            border-left: 3px solid transparent;
        }
        .nav-menu li a i {
            width: 20px;
            margin-right: 10px;
        }
        .nav-menu li a:hover, .nav-menu li.active a {
            background-color: #1e282c;
            color: white;
            border-left-color: #3c8dbc;
        }
        .nav-menu li.active a {
            background-color: #2e7d32;
            border-left-color: #1b5e20;
        }
        
        /* Main Content Styles */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
        }
        .topbar {
            background-color: white;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .topbar-right {
            display: flex;
            align-items: center;
        }
        .topbar-right span {
            margin-right: 10px;
            font-weight: 500;
            font-size: 14px;
        }
        .content-area {
            padding: 20px;
            flex-grow: 1;
        }
        
        /* Card & UI overrides */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .btn-green {
            background-color: #2e7d32;
            color: white;
        }
        .btn-green:hover {
            background-color: #1b5e20;
            color: white;
        }
        
        /* Action Buttons Styles */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.775rem;
            border-radius: 0.25rem;
            margin: 0 2px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #212529;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #4e555b;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        
        /* Table Enhancements */
        .table th, .table td {
            vertical-align: middle;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.02);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,.05);
        }
        
        /* Form Enhancements */
        .form-control:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
        }
        
        /* Badge Styles */
        .badge-secondary {
            background-color: #6c757d;
        }
        
        /* Animation Keyframes */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }
        
        .blink {
            animation: blink 1s infinite;
        }
        
        /* Enhanced Table Styling */
        .table td {
            vertical-align: middle;
        }
        
        .table .btn-sm {
            margin: 0 1px;
        }
        
        /* Success message styling */
        .alert-success {
            border-left: 4px solid #28a745;
        }
        
        /* Loading states */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: 100vh;
                z-index: 1000;
                left: -250px;
                box-shadow: 2px 0 5px rgba(0,0,0,0.5);
            }
            .sidebar.active {
                left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.active {
                display: block;
            }
            
            /* Sembunyikan elemen jika diperlukan saat mobile */
            .topbar-right span {
                display: none;
            }
        }
    </style>
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
