<?php
require_once '../koneksi.php';

// Processing data harus dilakukan sebelum output HTML apapun
// Proses hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = filter_input(INPUT_POST, 'hapus', FILTER_VALIDATE_INT);
    if ($id !== false && $id !== null && $id > 0 && $id != 1) {
        mysqli_query($koneksi, "DELETE FROM users WHERE id='" . (int)$id . "'");
    }
    header("location:user.php");
    exit();
}

// Proses tambah
if(isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($cek) == 0) {
        mysqli_query($koneksi, "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama', '$role')");
    }
    header("location:user.php");
    exit();
}

// Proses edit
if(isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    
    if(!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        mysqli_query($koneksi, "UPDATE users SET username='$username', password='$password', nama_lengkap='$nama', role='$role' WHERE id='$id'");
    } else {
        mysqli_query($koneksi, "UPDATE users SET username='$username', nama_lengkap='$nama', role='$role' WHERE id='$id'");
    }
    header("location:user.php");
    exit();
}

// Ambil data untuk edit jika ada parameter edit
$edit_data = null;
if(isset($_GET['edit'])) {
    $edit_id = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $edit_result = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$edit_id'");
    $edit_data = mysqli_fetch_assoc($edit_result);
}

$data = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id ASC");

// Setelah semua processing selesai, baru include header
require_once 'layout_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0" style="font-weight: 600;">Data Pengguna</h4>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card p-4">
            <h6 class="font-weight-bold mb-4">
                <?= $edit_data ? 'Edit Pengguna' : 'Tambah Pengguna Baru' ?>
            </h6>
            <form action="" method="post">
                <?php if($edit_data): ?>
                    <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group mb-3">
                    <label class="text-muted small font-weight-bold">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_lengkap" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['nama_lengkap']) : '' ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label class="text-muted small font-weight-bold">Username</label>
                    <input type="text" class="form-control" name="username" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['username']) : '' ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label class="text-muted small font-weight-bold">Role (Peran)</label>
                    <select class="form-control" name="role" required>
                        <option value="petugas" <?= ($edit_data && $edit_data['role'] == 'petugas') ? 'selected' : '' ?>>Petugas Lapangan</option>
                        <option value="admin" <?= ($edit_data && $edit_data['role'] == 'admin') ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>
                <div class="form-group mb-4">
                    <label class="text-muted small font-weight-bold">Password</label>
                    <input type="password" class="form-control" name="password" 
                           <?= $edit_data ? '' : 'required' ?>>
                    <?php if($edit_data): ?>
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex gap-2">
                    <?php if($edit_data): ?>
                        <button type="submit" name="edit" class="btn btn-warning flex-fill">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="user.php" class="btn btn-secondary flex-fill">
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
            <h6 class="font-weight-bold mb-4">Daftar Pengguna</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" style="font-size: 14px;">
                    <thead class="bg-light">
                        <tr>
                            <th width="10%">No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td>
                                <?php if($row['role'] == 'admin'): ?>
                                    <span class="badge badge-primary bg-primary text-white p-1">Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-success bg-success text-white p-1">Petugas</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['username'] != 'admin'): ?>
                                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                        <input type="hidden" name="hapus" value="<?= (int) $row['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Default Admin</span>
                                <?php endif; ?>
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
