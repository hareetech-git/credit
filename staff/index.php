<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Staff Login | Management Suite</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/favicon.ico">

    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --slate-900: #0f172a;
            --slate-600: #475569;
            --border-color: #e2e8f0;
        }

        body {
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .auth-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }

        .auth-header h4 {
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -0.02em;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--slate-600);
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--slate-900);
            box-shadow: none;
        }

        .btn-login {
            background-color: var(--slate-900);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .side-image {
            object-fit: contain;
            width: 100%;
            height: 100%;
            border-radius: 0 16px 16px 0;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-9">
                <div class="auth-card">
                    <div class="row g-0">
                        <div class="col-lg-6 p-4 p-md-5">
                            <div class="auth-header text-center text-lg-start mb-4">
                                <h4 class="fs-24">Staff Access</h4>
                                <p class="text-muted">Sign in with your staff account to continue.</p>
                            </div>

                            <?php if(isset($_GET['err'])): ?>
                                <div class="alert alert-danger border-0 bg-danger-subtle text-danger small py-2 mb-4">
                                    <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($_GET['err']) ?>
                                </div>
                            <?php endif; ?>

                            <form action="db/login.php" method="POST">
                                <div class="mb-4">
                                    <label for="username" class="form-label">Email Address</label>
                                    <input class="form-control" type="email" name="email" id="username" 
                                           required placeholder="Enter Your email">
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input class="form-control" type="password" name="password" id="password" 
                                           required placeholder="••••••••">
                                </div>

                                <button class="btn-login w-100 mb-3" type="submit">
                                    Sign In to Portal
                                </button>
                                
                                <p class="text-center text-muted small mb-0">
                                    Authorized staff access only.
                                </p>
                            </form>
                        </div>

                        <div class="col-lg-6 d-none d-lg-block bg-light">
                            <img src="assets/form_bg.png"  
                                 alt="Staff Portal" class="side-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.min.js"></script>
</body>
</html>
