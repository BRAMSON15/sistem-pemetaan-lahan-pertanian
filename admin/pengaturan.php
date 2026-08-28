<?php
require_once 'layout_header.php';
require_once '../koneksi.php';

$username = $_SESSION['username'];
$msg = "";

if(isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $password_baru = $_POST['password_baru'];
    
    if(!empty($password_baru)) {
        $hash = password_hash($password_baru, PASSWORD_BCRYPT);
        mysqli_query($koneksi, "UPDATE users SET nama_lengkap='$nama', password='$hash' WHERE username='$username'");
    } else {
        mysqli_query($koneksi, "UPDATE users SET nama_lengkap='$nama' WHERE username='$username'");
    }
    
    $_SESSION['nama_lengkap'] = $nama;
    $msg = "Profil berhasil diperbarui!";
}

$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'"));
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0" style="font-weight: 600;">Pengaturan Akun</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card p-4">
            <?php if($msg != ""): ?>
                <div class="alert alert-success"><?= $msg ?></div>
            <?php endif; ?>
            <form action="" method="post">
                <div class="form-group mb-3">
                    <label class="text-muted small font-weight-bold">Username (Tidak bisa diubah)</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                </div>
                <div class="form-group mb-3">
                    <label class="text-muted small font-weight-bold">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                </div>
                <div class="form-group mb-4">
                    <label class="text-muted small font-weight-bold">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" class="form-control" name="password_baru" placeholder="Masukkan password baru">
                </div>
                <button type="submit" name="simpan" class="btn btn-green w-100"><i class="fa fa-save"></i> Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
