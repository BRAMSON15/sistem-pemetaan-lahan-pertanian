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
    <div class="app-shell">
        <header class="header-green" role="banner">
            <div class="header-spacer" aria-hidden="true"></div>
            <h1 class="header-title">🌾 PETA LAHAN PERTANIAN</h1>
            <a href="<?= base_url('admin/login.php') ?>" class="btn-login" aria-label="Login Admin" title="Login Admin">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <span class="login-label">Login Admin</span>
            </a>
        </header>

        <main class="map-stage" aria-label="Peta lahan pertanian">
            <div id="map" aria-label="Peta lahan"></div>

            <div class="search-overlay" role="search">
                <label class="sr-only" for="input-kode-lahan">Cari data lahan</label>
                <i class="fa fa-search" aria-hidden="true"></i>
                <input type="text" id="input-kode-lahan" class="search-input" placeholder="Cari kode lahan, pemilik, atau kecamatan..." autocomplete="off">
                <button type="button" id="btn-cari-lahan" class="search-btn" title="Cari lahan" aria-label="Cari lahan">
                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                </button>
            </div>

            <div id="pesan-error-cari" role="alert" aria-live="assertive">Lahan tidak ditemukan!</div>

            <aside class="legend-overlay" aria-label="Legenda komoditas">
                <div class="legend-title"><i class="fa fa-map-marker" aria-hidden="true"></i>Legenda Komoditas</div>
                <?php
                $query = mysqli_query($koneksi, "SELECT * FROM komoditas ORDER BY nama_komoditas ASC");
                while ($row = mysqli_fetch_assoc($query)) {
                    $namaKomoditas = htmlspecialchars($row['nama_komoditas'], ENT_QUOTES, 'UTF-8');
                    $warna = htmlspecialchars($row['warna_polygon'], ENT_QUOTES, 'UTF-8');
                    echo "<div class='legend-item'>
                            <span class='legend-color' style='background-color:{$warna};'></span>
                            {$namaKomoditas}
                          </div>";
                }
                ?>
            </aside>
        </main>
    </div>

    <script src="assets/template/assets/js/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="assets/js/map.js"></script>
</body>
</html>
