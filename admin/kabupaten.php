<?php
require_once '../koneksi.php';

// Processing data harus dilakukan sebelum output HTML apapun
// Proses hapus
if(isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM kabupaten WHERE id='$id'");
    header("location:kabupaten.php");
    exit();
}

// Proses tambah
if(isset($_POST['tambah'])) {
    $id_provinsi = mysqli_real_escape_string($koneksi, $_POST['id_provinsi']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kabupaten']);
    mysqli_query($koneksi, "INSERT INTO kabupaten (id_provinsi, nama_kabupaten) VALUES ('$id_provinsi', '$nama')");
    header("location:kabupaten.php");
    exit();
}

// Proses edit
if(isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $id_provinsi = mysqli_real_escape_string($koneksi, $_POST['id_provinsi']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kabupaten']);
    mysqli_query($koneksi, "UPDATE kabupaten SET id_provinsi='$id_provinsi', nama_kabupaten='$nama' WHERE id='$id'");
    header("location:kabupaten.php");
    exit();
}

// Ambil data untuk edit jika ada parameter edit
$edit_data = null;
if(isset($_GET['edit'])) {
    $edit_id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $edit_result = mysqli_query($koneksi, "SELECT * FROM kabupaten WHERE id='$edit_id'");
    $edit_data = mysqli_fetch_assoc($edit_result);
}

$data = mysqli_query($koneksi, "SELECT k.*, p.nama_provinsi FROM kabupaten k JOIN provinsi p ON k.id_provinsi = p.id ORDER BY k.id ASC");
$provinsi = mysqli_query($koneksi, "SELECT * FROM provinsi");

// Setelah semua processing selesai, baru include header
require_once 'layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0" style="font-weight: 600;">Data Kabupaten</h4>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card p-4">
            <h6 class="font-weight-bold mb-4">
                <?= $edit_data ? 'Edit Kabupaten' : 'Tambah Kabupaten' ?>
            </h6>
            <form action="" method="post">
                <?php if($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group mb-3">
                    <label class="text-muted small font-weight-bold">Provinsi</label>
                    <select class="form-control" name="id_provinsi" required>
                        <option value="">-- Pilih Provinsi --</option>
                        <?php 
                        mysqli_data_seek($provinsi, 0); // Reset pointer
                        while($p = mysqli_fetch_assoc($provinsi)): 
                        ?>
                            <option value="<?= $p['id'] ?>" 
                                <?= ($edit_data && $edit_data['id_provinsi'] == $p['id']) ? 'selected' : '' ?>>
                                <?= $p['nama_provinsi'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group mb-4">
                    <label class="text-muted small font-weight-bold">Nama Kabupaten/Kota</label>
                    <input type="text" class="form-control" name="nama_kabupaten" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['nama_kabupaten']) : '' ?>" required>
                </div>
                
                <div class="d-flex gap-2">
                    <?php if($edit_data): ?>
                        <button type="submit" name="edit" class="btn btn-warning flex-fill">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="kabupaten.php" class="btn btn-secondary flex-fill">
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
            <h6 class="font-weight-bold mb-4">Daftar Kabupaten</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="font-size: 14px;">
                    <thead class="bg-light">
                        <tr>
                            <th width="10%">No</th>
                            <th>Provinsi</th>
                            <th>Nama Kabupaten</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_provinsi']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kabupaten']) ?></td>
                            <td class="text-center">
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Hapus kabupaten ini?')" title="Hapus">
                                    <i class="fa fa-trash"></i>
                                </a>
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
