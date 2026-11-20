<?php
include 'auth.php';
wajib_login();

if (isset($_POST['ganti'])) {
    cek_csrf();
    $lama  = $_POST['password_lama'] ?? '';
    $baru  = $_POST['password_baru'] ?? '';
    $ulang = $_POST['password_ulang'] ?? '';

    $stmt = $koneksi->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->bind_param('i', $_SESSION['id_user']);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();

    if (!$u || !password_verify($lama, $u['password'])) {
        set_flash('gagal', 'Password lama tidak cocok.');
    } elseif (strlen($baru) < 6) {
        set_flash('gagal', 'Password baru minimal 6 karakter.');
    } elseif ($baru !== $ulang) {
        set_flash('gagal', 'Konfirmasi password baru tidak sama.');
    } else {
        $hash = password_hash($baru, PASSWORD_DEFAULT);
        $up = $koneksi->prepare("UPDATE users SET password = ? WHERE id_user = ?");
        $up->bind_param('si', $hash, $_SESSION['id_user']);
        $up->execute();
        set_flash('sukses', 'Password berhasil diganti.');
    }
    pindah('ganti_password.php');
}

$aktif = '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StokMart - Ganti Password</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/fonts.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
</head>
<body>
<div class="app">
    <?php include 'sidebar.php'; ?>

    <main class="main">
        <header class="page-head">
            <div>
                <h1>Ganti password</h1>
                <p class="page-sub">Gunakan password yang sulit ditebak.</p>
            </div>
        </header>

        <?= tampil_flash(); ?>

        <section class="panel panel-form">
            <form method="POST">
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label" for="p_lama">Password lama</label>
                    <input type="password" id="p_lama" name="password_lama" class="form-control" autocomplete="current-password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="p_baru">Password baru</label>
                    <input type="password" id="p_baru" name="password_baru" class="form-control" minlength="6" autocomplete="new-password" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="p_ulang">Ulangi password baru</label>
                    <input type="password" id="p_ulang" name="password_ulang" class="form-control" minlength="6" autocomplete="new-password" required>
                </div>
                <button type="submit" name="ganti" class="btn btn-pandan">Simpan password</button>
            </form>
        </section>
    </main>
</div>
</body>
</html>
