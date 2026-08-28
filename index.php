<?php
require_once 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Lahan Pertanian GIS</title>
    <link rel="stylesheet" href="assets/template/assets/bootstrap/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/template/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel ="stylesheet" href="assets/css/map.css">
</head>
<body>

    <!-- Header -->
    <header class="header-green">
        <div></div>
        <h1 class="header-title">🌾 PETA LAHAN PERTANIAN</h1>
        <a href="<?= base_url('admin/login.php') ?>" class="btn-login" aria-label="Login Admin"><i class="fa fa-lock"></i><span class="login-label"> Login Admin</span></a>
    </header>

    <!-- Peta -->
    <div id="map"></div>

    <!-- Search -->
    <div class="search-overlay">
        <i class="fa fa-search" style="color:#aaa;margin-right:8px;"></i>
        <input type="text" id="input-kode-lahan" class="search-input" placeholder="Cari kode lahan, pemilik, atau kecamatan...">
        <button id="btn-cari-lahan" class="search-btn" title="Cari"><i class="fa fa-arrow-right"></i></button>
    </div>
    <div id="pesan-error-cari">Lahan tidak ditemukan!</div>

    <!-- Legenda -->
    <div class="legend-overlay">
        <div class="legend-title"><i class="fa fa-map-marker" style="color:#2e7d32;margin-right:6px;"></i>Legenda Komoditas</div>
        <?php
        $query = mysqli_query($koneksi, "SELECT * FROM komoditas ORDER BY nama_komoditas ASC");
        while($row = mysqli_fetch_assoc($query)) {
            echo "<div class='legend-item'>
                    <span class='legend-color' style='background-color:{$row['warna_polygon']};'></span>
                    {$row['nama_komoditas']}
                  </div>";
        }
        ?>
    </div>

    <!-- Scripts -->
    <script src="assets/template/assets/js/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="assets/js/map.js"></script>
</body>
</html>
