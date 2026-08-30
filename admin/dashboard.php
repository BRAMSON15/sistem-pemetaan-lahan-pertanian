<?php
require_once 'layout_header.php';
require_once '../koneksi.php';

// Menghitung data riil
$lahan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM lahan"))['total'];
$komoditas = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM komoditas"))['total'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users"))['total'];
$tot_kecamatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kecamatan"))['total'];

// Data grafik komoditas
$query_chart = mysqli_query($koneksi, "SELECT k.nama_komoditas, k.warna_polygon, COUNT(l.id) as jumlah FROM komoditas k LEFT JOIN lahan l ON k.id = l.id_komoditas GROUP BY k.id HAVING jumlah > 0");
$labels_pie = [];
$data_pie = [];
$colors_pie = [];
while($row = mysqli_fetch_assoc($query_chart)) {
    $labels_pie[] = $row['nama_komoditas'];
    $data_pie[] = $row['jumlah'];
    $colors_pie[] = $row['warna_polygon'];
}

// Data grafik lahan per kecamatan 
$query_kec = mysqli_query($koneksi, "SELECT kecamatan, COUNT(*) as jumlah FROM lahan WHERE kecamatan IS NOT NULL AND kecamatan != '' GROUP BY kecamatan");
$labels_bar = [];
$data_bar = [];
while($row = mysqli_fetch_assoc($query_kec)) {
    $labels_bar[] = $row['kecamatan'];
    $data_bar[] = $row['jumlah'];
}
// Jika belum ada data, beri label kosong agar tidak error
if(empty($labels_bar)) {
    $labels_bar = ['Belum ada data'];
    $data_bar = [0];
}

?>

<h4 class="mb-4" style="font-weight: 600;">Dashboard</h4>

<!-- Cards Row -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card p-3 d-flex flex-row align-items-center h-100 mb-0">
            <div class="mr-3 me-3 p-3 rounded text-center" style="background-color: #1976d2; color: white; min-width: 65px;">
                <i class="fa fa-map fa-2x"></i>
            </div>
            <div>
                <h3 class="mb-0 font-weight-bold"><?= $lahan ?></h3>
                <small class="text-muted">Total Lahan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card p-3 d-flex flex-row align-items-center h-100 mb-0">
            <div class="mr-3 me-3 p-3 rounded text-center" style="background-color: #4caf50; color: white; min-width: 65px;">
                <i class="fa fa-home fa-2x"></i>
            </div>
            <div>
                <h3 class="mb-0 font-weight-bold"><?= $tot_kecamatan ?></h3>
                <small class="text-muted">Total Kecamatan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card p-3 d-flex flex-row align-items-center h-100 mb-0">
            <div class="mr-3 me-3 p-3 rounded text-center" style="background-color: #ff9800; color: white; min-width: 65px;">
                <i class="fa fa-leaf fa-2x"></i>
            </div>
            <div>
                <h3 class="mb-0 font-weight-bold"><?= $komoditas ?></h3>
                <small class="text-muted">Total Komoditas</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card p-3 d-flex flex-row align-items-center h-100 mb-0">
            <div class="mr-3 me-3 p-3 rounded text-center" style="background-color: #9c27b0; color: white; min-width: 65px;">
                <i class="fa fa-user fa-2x"></i>
            </div>
            <div>
                <h3 class="mb-0 font-weight-bold"><?= $user ?></h3>
                <small class="text-muted">Total User</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-md-6">
        <div class="card p-4 h-100">
            <h6 class="font-weight-bold mb-4">Grafik Data Lahan per Kecamatan</h6>
            <canvas id="barChart"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4 h-100">
            <h6 class="font-weight-bold mb-4">Komoditas Terbanyak</h6>
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<?php 
ob_start(); 
?>
<script>
    window.dashboardBarLabels = <?= json_encode($labels_bar) ?>;
    window.dashboardBarData = <?= json_encode($data_bar) ?>;
    window.dashboardPieLabels = <?= json_encode($labels_pie) ?>;
    window.dashboardPieData = <?= json_encode($data_pie) ?>;
    window.dashboardPieColors = <?= json_encode($colors_pie) ?>;
</script>
<script src="../assets/js/chart.js"></script>

<?php 
$extra_scripts = ob_get_clean();
require_once 'layout_footer.php'; 
?>
