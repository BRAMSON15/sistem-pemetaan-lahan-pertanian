<?php
require_once 'layout_header.php';
require_once '../koneksi.php';

$data_lahan = mysqli_query($koneksi, "SELECT l.*, k.nama_komoditas FROM lahan l LEFT JOIN komoditas k ON l.id_komoditas = k.id ORDER BY l.id DESC");
?>

<div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 15px;">
    <h4 class="mb-0" style="font-weight: 600;">Laporan Data Lahan</h4>
    <button onclick="window.print()" class="btn btn-primary"><i class="fa fa-print"></i> Cetak Laporan</button>
</div>

<div class="card p-4">
    <div class="text-center mb-4 print-header" style="display:none;">
        <h3>SISTEM INFORMASI PEMETAAN LAHAN PERTANIAN</h3>
        <h4>Laporan Rekapitulasi Lahan</h4>
        <p>Tanggal Cetak: <?= date('d-m-Y') ?></p>
        <hr>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center" style="font-size: 14px;">
            <thead class="bg-light">
                <tr>
                    <th width="5%">No</th>
                    <th>Kode</th>
                    <th>Nama Lahan/Pemilik</th>
                    <th>Luas (Ha)</th>
                    <th>Kecamatan</th>
                    <th>Komoditas</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; $total_luas = 0; while($row = mysqli_fetch_assoc($data_lahan)): $total_luas += $row['luas']; ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['kode_lahan'] ?? '-') ?></td>
                    <td class="text-left"><?= htmlspecialchars($row['nama_pemilik']) ?></td>
                    <td><?= $row['luas'] ?></td>
                    <td><?= htmlspecialchars($row['kecamatan'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama_komoditas']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total Luas Lahan:</th>
                    <th><?= $total_luas ?> Ha</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .card, .card * { visibility: visible; }
    .card { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
    .btn { display: none !important; }
    .print-header { display: block !important; }
}
</style>

<?php require_once 'layout_footer.php'; ?>
