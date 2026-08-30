<?php
require_once '../koneksi.php';

// Processing data harus dilakukan sebelum output HTML apapun
if(isset($_POST['hapus']) || isset($_GET['hapus'])) {
    $source = isset($_POST['hapus']) ? INPUT_POST : INPUT_GET;
    $id = filter_input($source, 'hapus', FILTER_VALIDATE_INT);
    $status = 'gagal';

    if($id !== false && $id !== null && $id > 0) {
        $delete_query = mysqli_query($koneksi, "DELETE FROM lahan WHERE id=$id");
        if($delete_query && mysqli_affected_rows($koneksi) === 1) {
            $status = 'berhasil';
        }
    }

    header("location:lahan.php?hapus_status=$status");
    exit();
}

if(isset($_POST['simpan'])) {
    $kode_lahan = mysqli_real_escape_string($koneksi, $_POST['kode_lahan']);
    $pemilik = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
    $kecamatan = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $luas = mysqli_real_escape_string($koneksi, $_POST['luas']);
    $komoditas = mysqli_real_escape_string($koneksi, $_POST['id_komoditas']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $geojson = mysqli_real_escape_string($koneksi, $_POST['geojson']); 

    mysqli_query($koneksi, "INSERT INTO lahan (kode_lahan, nama_pemilik, kecamatan, luas, id_komoditas, keterangan, geojson) VALUES ('$kode_lahan', '$pemilik', '$kecamatan', '$luas', '$komoditas', '$keterangan', '$geojson')");
    header("location:lahan.php");
    exit();
}

// Proses edit
if(isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $kode_lahan = mysqli_real_escape_string($koneksi, $_POST['kode_lahan']);
    $pemilik = mysqli_real_escape_string($koneksi, $_POST['nama_pemilik']);
    $kecamatan = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $luas = mysqli_real_escape_string($koneksi, $_POST['luas']);
    $komoditas = mysqli_real_escape_string($koneksi, $_POST['id_komoditas']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $geojson = mysqli_real_escape_string($koneksi, $_POST['geojson']); 

    mysqli_query($koneksi, "UPDATE lahan SET kode_lahan='$kode_lahan', nama_pemilik='$pemilik', kecamatan='$kecamatan', luas='$luas', id_komoditas='$komoditas', keterangan='$keterangan', geojson='$geojson' WHERE id='$id'");
    header("location:lahan.php");
    exit();
}

// Ambil data untuk edit jika ada parameter edit
$edit_data = null;
if(isset($_GET['edit'])) {
    $edit_id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $edit_result = mysqli_query($koneksi, "SELECT * FROM lahan WHERE id='$edit_id'");
    $edit_data = mysqli_fetch_assoc($edit_result);
}

$is_tambah = isset($_GET['tambah_lahan']);
$is_edit = isset($_GET['edit']);

// Setelah semua processing selesai, baru include header
require_once 'layout_header.php';
?>

<?php if($is_tambah || $is_edit): ?>
    <!-- Halaman Form Tambah/Edit Data Lahan -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0" style="font-weight: 600;">
            <?= $is_edit ? 'Edit Data Lahan' : 'Tambah Data Lahan' ?>
        </h4>
        <a href="lahan.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>

    <div class="card p-4">
        <form action="" method="post" id="form-lahan">
            <?php if($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Kode Lahan</label>
                        <input type="text" class="form-control" name="kode_lahan" 
                               value="<?= $edit_data ? htmlspecialchars($edit_data['kode_lahan']) : '' ?>"
                               placeholder="Contoh: LHN-001" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Nama Pemilik / Lahan</label>
                        <input type="text" class="form-control" name="nama_pemilik" 
                               value="<?= $edit_data ? htmlspecialchars($edit_data['nama_pemilik']) : '' ?>"
                               placeholder="Masukkan nama lahan/pemilik" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Luas Lahan (Ha)</label>
                        <input type="number" step="0.01" class="form-control" name="luas" 
                               value="<?= $edit_data ? $edit_data['luas'] : '' ?>"
                               placeholder="Masukkan luas lahan" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Kecamatan</label>
                        <select class="form-control" name="kecamatan" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            <?php 
                            $q_kec = mysqli_query($koneksi, "SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC");
                            while($kec = mysqli_fetch_assoc($q_kec)): 
                            ?>
                                <option value="<?= $kec['nama_kecamatan'] ?>" 
                                    <?= ($edit_data && $edit_data['kecamatan'] == $kec['nama_kecamatan']) ? 'selected' : '' ?>>
                                    <?= $kec['nama_kecamatan'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Komoditas</label>
                        <select class="form-control" name="id_komoditas" required>
                            <option value="">-- Pilih Komoditas --</option>
                            <?php 
                            $q_kom = mysqli_query($koneksi, "SELECT * FROM komoditas");
                            while($k = mysqli_fetch_assoc($q_kom)): 
                            ?>
                                <option value="<?= $k['id'] ?>" 
                                    <?= ($edit_data && $edit_data['id_komoditas'] == $k['id']) ? 'selected' : '' ?>>
                                    <?= $k['nama_komoditas'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label class="text-muted small font-weight-bold">Deskripsi</label>
                        <textarea class="form-control" name="keterangan" rows="4" placeholder="Masukkan deskripsi lahan"><?= $edit_data ? htmlspecialchars($edit_data['keterangan']) : '' ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="text-muted small font-weight-bold">Peta & Kordinat Polygon (Leaflet Draw)</label>
                        <!-- Peta Leaflet untuk menggambar polygon -->
                        <div id="map-admin" style="height: 350px; border-radius: 8px; border: 1px solid #ced4da; z-index:1;"></div>
                        <input type="hidden" name="geojson" id="geojson" 
                               value="<?= $edit_data ? htmlspecialchars($edit_data['geojson']) : '' ?>" required>
                        <div id="warning-geo" class="text-danger small mt-2" style="font-weight: bold;">
                            <i class="fa fa-info-circle"></i> Gunakan alat gambar (kotak/polygon) di atas peta untuk memetakan area lahan.
                        </div>

                        <!-- Fitur GPS Walking Survey -->
                        <div id="gps-controls" class="mt-3 p-3 border rounded bg-light" style="border-left: 4px solid #2e7d32 !important;">
                            <span class="font-weight-bold d-block mb-2" style="color: #2e7d32;"><i class="fa fa-location-arrow"></i> Mode GPS Walking Survey</span>
                            <p class="small text-muted mb-2">Bawa perangkat Anda mengelilingi batas lahan untuk memetakan secara otomatis.</p>
                            
                            <!-- Sensitivitas GPS -->
                            <div class="mb-2">
                                <label class="small text-muted font-weight-bold d-block mb-1">Sensitivitas GPS:</label>
                                <div class="btn-group btn-group-sm" role="group" id="gps-sensitivity-group">
                                    <button type="button" class="btn btn-outline-success gps-sens-btn" data-level="high" title="Akurasi < 20m — sinyal kuat">🟢 Tinggi</button>
                                    <button type="button" class="btn btn-outline-warning gps-sens-btn active" data-level="medium" title="Akurasi < 50m — sinyal normal" style="background-color: #ffc107; color: #212529;">🟡 Sedang</button>
                                    <button type="button" class="btn btn-outline-danger gps-sens-btn" data-level="low" title="Akurasi < 150m — sinyal lemah">🔴 Rendah</button>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap mb-2" style="gap: 5px;">
                                <button type="button" id="btn-start-gps" class="btn btn-sm btn-primary"><i class="fa fa-play"></i> Mulai Jalan</button>
                                <button type="button" id="btn-stop-gps" class="btn btn-sm btn-danger" disabled><i class="fa fa-stop"></i> Berhenti & Buat Area</button>
                                <button type="button" id="btn-reset-gps" class="btn btn-sm btn-warning" disabled><i class="fa fa-refresh"></i> Reset</button>
                                <button type="button" id="btn-add-manual" class="btn btn-sm btn-info" disabled title="Klik di peta untuk tambah titik manual"><i class="fa fa-map-pin"></i> + Titik Manual</button>
                            </div>

                            <!-- Signal Bar Indicator -->
                            <div id="gps-signal-bar" class="mb-2" style="display: none;">
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <span class="small font-weight-bold">Sinyal:</span>
                                    <div style="display: flex; align-items: flex-end; gap: 2px; height: 16px;">
                                        <div id="sig-bar-1" style="width: 4px; height: 4px; background: #ccc; border-radius: 1px;"></div>
                                        <div id="sig-bar-2" style="width: 4px; height: 7px; background: #ccc; border-radius: 1px;"></div>
                                        <div id="sig-bar-3" style="width: 4px; height: 10px; background: #ccc; border-radius: 1px;"></div>
                                        <div id="sig-bar-4" style="width: 4px; height: 13px; background: #ccc; border-radius: 1px;"></div>
                                        <div id="sig-bar-5" style="width: 4px; height: 16px; background: #ccc; border-radius: 1px;"></div>
                                    </div>
                                    <span id="signal-label" class="small font-weight-bold text-muted">--</span>
                                </div>
                            </div>

                            <!-- Progress bar inisialisasi -->
                            <div id="gps-init-progress" class="mb-2" style="display: none;">
                                <div class="progress" style="height: 6px;">
                                    <div id="gps-init-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: 0%;"></div>
                                </div>
                                <small id="gps-init-text" class="text-muted">Mencari sinyal terbaik...</small>
                            </div>

                            <div id="gps-status" class="small font-weight-bold mt-2 text-secondary">Status: Menunggu instruksi...</div>
                            <div id="gps-stats" class="small text-muted mt-1" style="display: none;">
                                📊 Titik: <span id="stat-points">0</span> | Jarak: <span id="stat-distance">0</span>m
                            </div>
                            
                            <!-- Tips sinyal lemah -->
                            <div id="gps-tips" class="small text-muted mt-2 p-2 rounded" style="background: #fff3cd; display: none;">
                                <i class="fa fa-lightbulb-o text-warning"></i> <strong>Tips sinyal lemah:</strong> Bergerak perlahan, jauhi bangunan tinggi, atau gunakan "Titik Manual" dengan klik di peta.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex mt-4">
                        <?php if($is_edit): ?>
                            <button type="submit" name="edit" class="btn btn-warning mr-2 px-4">
                                <i class="fa fa-save"></i> Update
                            </button>
                        <?php else: ?>
                            <button type="submit" name="simpan" class="btn btn-green mr-2 px-4">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        <?php endif; ?>
                        <a href="lahan.php" class="btn btn-light px-4 border">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php 
    ob_start();
    ?>
    <script>
        window.existingLahanGeojson = <?= !empty($edit_data['geojson']) ? $edit_data['geojson'] : 'null' ?>;
    </script>
    <script src="../assets/js/lahan.js"></script>
   
    <?php 
    $extra_scripts = ob_get_clean();
    ?>

<?php else: ?>
    <!-- Halaman Data Lahan (Tabel) -->
    <?php if(isset($_GET['hapus_status'])): ?>
        <div class="alert <?= $_GET['hapus_status'] === 'berhasil' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
            <?= $_GET['hapus_status'] === 'berhasil' ? 'Data lahan berhasil dihapus.' : 'Data lahan gagal dihapus atau tidak ditemukan.' ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0" style="font-weight: 600;">Data Lahan</h4>
    </div>

    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 15px;">
            <a href="?tambah_lahan=1" class="btn btn-green"><i class="fa fa-plus"></i> Tambah Data</a>
            <div class="input-group" style="width: 250px; max-width: 100%;">
                <input type="text" class="form-control" placeholder="Cari data lahan...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-center" style="font-size: 14px;">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode</th>
                        <th>Nama Lahan</th>
                        <th>Luas (Ha)</th>
                        <th>Kecamatan</th>
                        <th>Komoditas</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $data_lahan = mysqli_query($koneksi, "SELECT l.*, k.nama_komoditas FROM lahan l LEFT JOIN komoditas k ON l.id_komoditas = k.id ORDER BY l.id DESC");
                    $no = 1; 
                    while($row = mysqli_fetch_assoc($data_lahan)): 
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['kode_lahan'] ?? '-') ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['nama_pemilik']) ?></td>
                        <td><?= $row['luas'] ?></td>
                        <td><?= htmlspecialchars($row['kecamatan'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['nama_komoditas']) ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <form method="post" action="lahan.php" class="d-inline" onsubmit="return confirm('Hapus lahan ini? Tindakan ini tidak dapat dibatalkan.');">
                                <input type="hidden" name="hapus" value="<?= (int) $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center flex-wrap mt-3" style="gap: 10px;">
            <small class="text-muted">Showing data entries</small>
            <ul class="pagination pagination-sm mb-0">
                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'layout_footer.php'; ?>
