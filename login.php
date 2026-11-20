<?php
require_once 'auth.php';

// Jika sudah login, langsung arahkan ke dashboard
if (!empty($_SESSION['id_user']) && !empty($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$info  = isset($_GET['keluar']) ? "Anda sudah keluar dengan aman. Sampai jumpa lagi!" : "";

if (isset($_POST['login'])) {
    $user_input = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? '';
    $stmt = $koneksi->prepare("SELECT id_user,nama_lengkap,username,password,role FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param('s',$user_input);
    $stmt->execute();
    $row=$stmt->get_result()->fetch_assoc();
    $cocok = false;
    if ($row) {
        if (password_get_info($row['password'])['algo'] !== null) {
            // Password sudah berupa hash (cara normal)
            $cocok = password_verify($password_input, $row['password']);
        } elseif (hash_equals($row['password'], $password_input)) {
            // Password lama masih teks biasa: terima sekali, lalu ubah menjadi hash
            $cocok = true;
            $hash_baru = password_hash($password_input, PASSWORD_DEFAULT);
            $up = $koneksi->prepare("UPDATE users SET password=? WHERE id_user=?");
            $up->bind_param('si', $hash_baru, $row['id_user']);
            $up->execute();
        }
    }
    if ($cocok) {
        session_regenerate_id(true);
        $_SESSION['login']=true; $_SESSION['id_user']=(int)$row['id_user'];
        $_SESSION['nama']=$row['nama_lengkap']; $_SESSION['role']=$row['role'];
        $_SESSION['csrf']=bin2hex(random_bytes(32));
        header("Location: index.php"); exit;
    }
    $error="Username atau password yang Anda masukkan salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StokMart - Masuk</title>
    <meta name="theme-color" content="#4F46E5">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/fonts.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">
    <style>
        body { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; position:relative; overflow-x:hidden;
               background: linear-gradient(135deg, #6366F1 0%, #4F46E5 40%, #312E81 100%); }
        body::before, body::after { content:""; position:absolute; border-radius:50%; pointer-events:none; }
        body::before { width:460px; height:460px; top:-140px; right:-120px; background:radial-gradient(circle, rgba(251,191,36,.35), transparent 70%); }
        body::after  { width:520px; height:520px; bottom:-200px; left:-160px; background:radial-gradient(circle, rgba(124,131,255,.45), transparent 70%); }

        .login-shell { position:relative; z-index:1; display:grid; grid-template-columns:1.05fr 1fr; width:100%; max-width:900px;
                       background:#fff; border-radius:24px; overflow:hidden; box-shadow:0 30px 70px rgba(15,12,60,.45); animation:masuk .5s ease-out both; }
        .login-hero { position:relative; color:#fff; padding:44px 38px; display:flex; flex-direction:column; justify-content:center; gap:18px;
                      background:linear-gradient(160deg, #4F46E5 0%, #3730A3 60%, #312E81 100%); overflow:hidden; }
        .login-hero::before { content:""; position:absolute; width:260px; height:260px; border-radius:50%; right:-90px; top:-90px; background:rgba(251,191,36,.22); }
        .login-hero::after  { content:""; position:absolute; width:200px; height:200px; border-radius:50%; left:-70px; bottom:-70px; background:rgba(255,255,255,.1); }
        .login-hero > * { position:relative; z-index:1; }
        .login-logo { width:92px; height:92px; border-radius:24px; display:block; box-shadow:0 14px 30px rgba(0,0,0,.35); animation:melayang 4s ease-in-out infinite; }
        .login-hero h1 { font-size:38px; font-weight:700; margin:0; color:#fff; }
        .login-hero h1 span { color:var(--kunyit); }
        .login-hero p { margin:0; color:rgba(255,255,255,.85); }
        .fitur { list-style:none; padding:0; margin:6px 0 0; display:grid; gap:10px; }
        .fitur li { display:flex; align-items:center; gap:10px; font-size:14px; color:rgba(255,255,255,.92); }
        .fitur i { width:28px; height:28px; border-radius:8px; background:rgba(255,255,255,.16); display:grid; place-items:center; color:var(--kunyit); flex:none; }

        .login-form { padding:44px 40px; display:flex; flex-direction:column; justify-content:center; }
        .login-form h2 { font-size:26px; font-weight:700; color:var(--pandan-deep); margin:0 0 4px; }
        .login-form .form-control { padding:11px 14px; border-radius:var(--radius-ctl); border-color:var(--line); background:#FAFAFF; }
        .login-form .input-icon { position:relative; }
        .login-form .input-icon > i.lead-i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none; }
        .login-form .input-icon .form-control { padding-left:42px; }
        .btn-login { background:linear-gradient(135deg, var(--pandan-mid), var(--pandan)); color:#fff; font-weight:600; border:0; border-radius:var(--radius-ctl);
                     box-shadow:0 8px 20px rgba(79,70,229,.35); transition:transform .15s, box-shadow .15s; }
        .btn-login:hover { color:#fff; transform:translateY(-1px); box-shadow:0 12px 24px rgba(79,70,229,.45); }
        .pw-wrap { position:relative; }
        .pw-wrap .form-control { padding-right:46px; }
        .pw-toggle { position:absolute; right:6px; top:50%; transform:translateY(-50%); border:0; background:none; color:var(--muted); padding:6px 10px; }
        .pw-toggle:hover { color:var(--pandan); }

        @keyframes masuk { from { opacity:0; transform:translateY(18px) scale(.98); } to { opacity:1; transform:none; } }
        @keyframes melayang { 0%,100% { transform:translateY(0) rotate(-3deg); } 50% { transform:translateY(-8px) rotate(3deg); } }
        @media (max-width:767.98px) {
            .login-shell { grid-template-columns:1fr; max-width:440px; }
            .login-hero { padding:28px 28px 24px; flex-direction:row; flex-wrap:wrap; align-items:center; gap:14px; }
            .login-logo { width:60px; height:60px; border-radius:16px; }
            .login-hero h1 { font-size:28px; }
            .login-hero p, .fitur { display:none; }
            .login-form { padding:28px; }
        }
        @media (prefers-reduced-motion: reduce) { .login-shell, .login-logo { animation:none; } }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="login-hero" aria-label="Tentang StokMart">
            <img class="login-logo" src="assets/img/logo.svg" alt="Logo StokMart">
            <div>
                <h1>Stok<span>Mart</span></h1>
                <p class="mt-1">Kelola toko sembako lebih rapi, cepat, dan tenang.</p>
            </div>
            <ul class="fitur">
                <li><i class="bi bi-receipt"></i> Catat penjualan kasir dengan mudah</li>
                <li><i class="bi bi-boxes"></i> Pantau stok dan barang yang menipis</li>
                <li><i class="bi bi-bar-chart-line"></i> Lihat laporan untung rugi kapan saja</li>
            </ul>
        </section>

        <section class="login-form">
            <h2>Selamat datang</h2>
            <p class="text-muted small mb-4">Masuk dengan akun yang diberikan pemilik toko.</p>

            <?php if ($info != ""): ?>
                <div class="notice notice-ok py-2 small"><i class="bi bi-check-circle"></i><span><?= htmlspecialchars($info, ENT_QUOTES, 'UTF-8'); ?></span></div>
            <?php endif; ?>
            <?php if ($error != ""): ?>
                <div class="alert alert-danger py-2 small" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label" for="u">Username / Email</label>
                    <div class="input-icon">
                        <i class="bi bi-person lead-i"></i>
                        <input type="text" id="u" name="username" autofocus class="form-control" autocomplete="username" placeholder="Masukkan username atau email" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="p">Password</label>
                    <div class="input-icon pw-wrap">
                        <i class="bi bi-lock lead-i"></i>
                        <input type="password" id="p" name="password" class="form-control" autocomplete="current-password" placeholder="Masukkan password" required>
                        <button type="button" class="pw-toggle" id="pwToggle" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <button type="submit" name="login" class="btn btn-login w-100 py-2"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
            </form>
        </section>
    </main>
    <script>
        document.getElementById('pwToggle').addEventListener('click', function () {
            var i = document.getElementById('p'), tampil = i.type === 'password';
            i.type = tampil ? 'text' : 'password';
            this.firstElementChild.className = 'bi ' + (tampil ? 'bi-eye-slash' : 'bi-eye');
            this.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
        });
    </script>
</body>
</html>
