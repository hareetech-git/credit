<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>DSA Login | Management Suite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../staff/assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../staff/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
        :root { --slate-900:#0f172a; --slate-600:#475569; --border-color:#e2e8f0; }
        body { background-color:#f8fafc; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .auth-card { background:#fff; border:1px solid var(--border-color); border-radius:16px; max-width:900px; width:100%; }
    </style>
</head>
<body>
<div class="container"><div class="row justify-content-center"><div class="col-xxl-9"><div class="auth-card"><div class="row g-0">
<div class="col-lg-6 p-4 p-md-5">
    <h4 class="fs-24 fw-bold" style="color:var(--slate-900)">DSA Access</h4>
    <p class="text-muted">Sign in with your DSA account to continue.</p>
    <?php if(isset($_GET['err'])): ?>
        <div class="alert alert-danger border-0 bg-danger-subtle text-danger small py-2 mb-4"><?= htmlspecialchars($_GET['err']) ?></div>
    <?php endif; ?>
    <form action="db/login.php" method="POST">
        <div class="mb-4"><label class="form-label">Email Address</label><input class="form-control" type="email" name="email" required></div>
        <div class="mb-4"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
        <button class="btn btn-dark w-100" type="submit">Sign In to DSA Portal</button>
    </form>
</div>
<div class="col-lg-6 d-none d-lg-block bg-light d-flex align-items-center justify-content-center">
    <img src="../admin/assets/udhaar_logo.png" alt="DSA Portal" style="max-width:75%;">
</div>
</div></div></div></div></div>
<script src="../staff/assets/js/vendor.min.js"></script>
<script src="../staff/assets/js/app.min.js"></script>
</body>
</html>
