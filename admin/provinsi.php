<?php
require_once '../koneksi.php';

// Processing data harus dilakukan sebelum output HTML apapun
// Proses hapus
if(isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM provinsi WHERE id='$id'");
    header("location:provinsi.php");
    exit();
}

// Proses tambah
if(isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_provinsi']);
    mysqli_query($koneksi, "INSERT INTO provinsi (nama_provinsi) VALUES ('$nama')");
    header("location:provinsi.php");
    exit();
}

// Proses edit
if(isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_provinsi']);
    mysqli_query($koneksi, "UPDATE provinsi SET nama_provinsi='$nama' WHERE id='$id'");
    header("location:provinsi.php");
    exit();
}

// Ambil data untuk edit jika ada parameter edit
$edit_data = null;
if(isset($_GET['edit'])) {
    $edit_id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $edit_result = mysqli_query($koneksi, "SELECT * FROM provinsi WHERE id='$edit_id'");
    $edit_data = mysqli_fetch_assoc($edit_result);
}

$data = mysqli_query($koneksi, "SELECT * FROM provinsi ORDER BY id ASC");

// Setelah semua processing selesai, baru include header
require_once 'layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0" style="font-weight: 600;">Data Provinsi</h4>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card p-4">
            <h6 class="font-weight-bold mb-4">
                <?= $edit_data ? 'Edit Provinsi' : 'Tambah Provinsi' ?>
            </h6>
            <form action="" method="post">
                <?php if($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group mb-4">
                    <label class="text-muted small font-weight-bold">Nama Provinsi</label>
                    <input type="text" class="form-control" name="nama_provinsi" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['nama_provinsi']) : '' ?>" required>
                </div>
                
                <div class="d-flex gap-2">
                    <?php if($edit_data): ?>
                        <button type="submit" name="edit" class="btn btn-warning flex-fill">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="provinsi.php" class="btn btn-secondary flex-fill">
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
            <h6 class="font-weight-bold mb-4">Daftar Provinsi</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="font-size: 14px;">
                    <thead class="bg-light">
                        <tr>
                            <th width="10%">No</th>
                            <th>Nama Provinsi</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_provinsi']) ?></td>
                            <td class="text-center">
                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Hapus provinsi ini?')" title="Hapus">
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
