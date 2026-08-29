<?php
require_once '../koneksi.php';

// Processing data harus dilakukan sebelum output HTML apapun
// Proses hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = filter_input(INPUT_POST, 'hapus', FILTER_VALIDATE_INT);
    if ($id !== false && $id !== null && $id > 0) {
        mysqli_query($koneksi, "DELETE FROM kecamatan WHERE id='" . (int)$id . "'");
    }
    header("location:kecamatan.php");
    exit();
}

// Proses tambah
if(isset($_POST['tambah'])) {
    $id_kabupaten = mysqli_real_escape_string($koneksi, $_POST['id_kabupaten']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kecamatan']);
    mysqli_query($koneksi, "INSERT INTO kecamatan (id_kabupaten, nama_kecamatan) VALUES ('$id_kabupaten', '$nama')");
    header("location:kecamatan.php");
    exit();
}

// Proses edit
if(isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $id_kabupaten = mysqli_real_escape_string($koneksi, $_POST['id_kabupaten']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kecamatan']);
    mysqli_query($koneksi, "UPDATE kecamatan SET id_kabupaten='$id_kabupaten', nama_kecamatan='$nama' WHERE id='$id'");
    header("location:kecamatan.php");
    exit();
}

// Ambil data untuk edit jika ada parameter edit
$edit_data = null;
if(isset($_GET['edit'])) {
    $edit_id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $edit_result = mysqli_query($koneksi, "SELECT * FROM kecamatan WHERE id='$edit_id'");
    $edit_data = mysqli_fetch_assoc($edit_result);
}

$data = mysqli_query($koneksi, "SELECT kec.*, kab.nama_kabupaten FROM kecamatan kec JOIN kabupaten kab ON kec.id_kabupaten = kab.id ORDER BY kec.id ASC");
$kabupaten = mysqli_query($koneksi, "SELECT * FROM kabupaten");

// Setelah semua processing selesai, baru include header
require_once 'layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0" style="font-weight: 600;">Data Kecamatan</h4>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card p-4">
            <h6 class="font-weight-bold mb-4">
                <?= $edit_data ? 'Edit Kecamatan' : 'Tambah Kecamatan' ?>
            </h6>
            <form action="" method="post">
                <?php if($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group mb-3">
                    <label class="text-muted small font-weight-bold">Kabupaten</label>
                    <select class="form-control" name="id_kabupaten" required>
                        <option value="">-- Pilih Kabupaten --</option>
                        <?php 
                        mysqli_data_seek($kabupaten, 0); // Reset pointer
                        while($k = mysqli_fetch_assoc($kabupaten)): 
                        ?>
                            <option value="<?= $k['id'] ?>" 
                                <?= ($edit_data && $edit_data['id_kabupaten'] == $k['id']) ? 'selected' : '' ?>>
                                <?= $k['nama_kabupaten'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group mb-4">
                    <label class="text-muted small font-weight-bold">Nama Kecamatan</label>
                    <input type="text" class="form-control" name="nama_kecamatan" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['nama_kecamatan']) : '' ?>" required>
                </div>
                
                <div class="d-flex gap-2">
                    <?php if($edit_data): ?>
                        <button type="submit" name="edit" class="btn btn-warning flex-fill">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="kecamatan.php" class="btn btn-secondary flex-fill">
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

    <div class="col-md-8">
        <div class="card p-4">
            <h6 class="font-weight-bold mb-4">Daftar Kecamatan</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="font-size: 14px;">
                    <thead class="bg-light">
                        <tr>
                            <th width="10%">No</th>
                            <th>Kabupaten</th>
                            <th>Nama Kecamatan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_kabupaten']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kecamatan']) ?></td>
                            <td class="text-center">
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form method="post" action="" class="d-inline" onsubmit="return confirm('Hapus kecamatan ini?')">
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

<?php require_once 'layout_footer.php'; ?>
