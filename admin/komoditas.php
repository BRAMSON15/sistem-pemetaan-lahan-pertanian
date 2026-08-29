<?php
require_once '../koneksi.php';

// Processing data harus dilakukan sebelum output HTML apapun
// Proses hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = filter_input(INPUT_POST, 'hapus', FILTER_VALIDATE_INT);
    if ($id !== false && $id !== null && $id > 0) {
        mysqli_query($koneksi, "DELETE FROM komoditas WHERE id='" . (int)$id . "'");
    }
    header("location:komoditas.php");
    exit();
}

// Proses tambah
if(isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_komoditas']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $warna = mysqli_real_escape_string($koneksi, $_POST['warna_polygon']);
    mysqli_query($koneksi, "INSERT INTO komoditas (nama_komoditas, deskripsi, warna_polygon) VALUES ('$nama', '$deskripsi', '$warna')");
    header("location:komoditas.php");
    exit();
}

// Proses edit
if(isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_komoditas']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $warna = mysqli_real_escape_string($koneksi, $_POST['warna_polygon']);
    mysqli_query($koneksi, "UPDATE komoditas SET nama_komoditas='$nama', deskripsi='$deskripsi', warna_polygon='$warna' WHERE id='$id'");
    header("location:komoditas.php");
    exit();
}

// Ambil data untuk edit jika ada parameter edit
$edit_data = null;
if(isset($_GET['edit'])) {
    $edit_id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $edit_result = mysqli_query($koneksi, "SELECT * FROM komoditas WHERE id='$edit_id'");
    $edit_data = mysqli_fetch_assoc($edit_result);
}

$data_komoditas = mysqli_query($koneksi, "SELECT * FROM komoditas ORDER BY id DESC");

// Setelah semua processing selesai, baru include header
require_once 'layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0" style="font-weight: 600;">Data Komoditas</h4>
</div>

<div class="row">
    <!-- Form Tambah/Edit -->
    <div class="col-md-4 mb-4">
        <div class="card p-4">
            <h6 class="font-weight-bold mb-4">
                <?= $edit_data ? 'Edit Komoditas' : 'Tambah Komoditas' ?>
            </h6>
            <form action="" method="post">
                <?php if($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group mb-3">
                    <label class="text-muted small font-weight-bold">Nama Komoditas</label>
                    <input type="text" class="form-control" name="nama_komoditas" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['nama_komoditas']) : '' ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label class="text-muted small font-weight-bold">Deskripsi</label>
                    <textarea class="form-control" name="deskripsi" rows="3"><?= $edit_data ? htmlspecialchars($edit_data['deskripsi']) : '' ?></textarea>
                </div>
                <div class="form-group mb-4">
                    <label class="text-muted small font-weight-bold">Warna Polygon</label>
                    <input type="color" class="form-control p-1" name="warna_polygon" 
                           value="<?= $edit_data ? $edit_data['warna_polygon'] : '#3388ff' ?>" style="height: 40px;">
                </div>
                
                <div class="d-flex gap-2">
                    <?php if($edit_data): ?>
                        <button type="submit" name="edit" class="btn btn-warning flex-fill">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="komoditas.php" class="btn btn-secondary flex-fill">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    <?php else: ?>
                        <button type="submit" name="tambah" class="btn btn-green w-100">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="col-md-8">
        <div class="card p-4">
            <h6 class="font-weight-bold mb-4">Daftar Komoditas</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="font-size: 14px;">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Komoditas</th>
                            <th>Deskripsi</th>
                            <th width="15%">Warna</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($data_komoditas)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_komoditas']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td>
                                <span style="display:inline-block; width:15px; height:15px; background-color:<?= $row['warna_polygon'] ?>; border-radius:50%; vertical-align:middle; margin-right:5px;"></span>
                                <?= $row['warna_polygon'] ?>
                            </td>
                            <td class="text-center">
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form method="post" action="" class="d-inline" onsubmit="return confirm('Hapus komoditas ini?')">
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
        </div>
    </div>
</div>

<?php 
require_once 'layout_footer.php'; 
?>
