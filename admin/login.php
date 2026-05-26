<?php
session_start();
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login — Pinterest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        .login-box {
            width: 420px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        .brand { text-align: center; margin-bottom: 24px; }
        .brand .logo { font-size: 36px; font-weight: 900; color: #E60023; letter-spacing: -1px; }
        .brand .sub  { font-size: 13px; color: #999; margin-top: 4px; }
        h2 { text-align: center; margin-bottom: 24px; color: #111; font-size: 20px; font-weight: 700; }
        .form-control { border-radius: 8px; padding: 11px 14px; font-size: 14px; }
        .form-label   { font-weight: 600; font-size: 13px; color: #444; }
        .btn-login {
            background: #E60023; color: white; width: 100%; border: none;
            padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: background 0.2s; margin-top: 6px;
        }
        .btn-login:hover { background: #ad081b; }
        .back-link {
            display: block; text-align: center; margin-top: 18px;
            color: #999; font-size: 13px; text-decoration: none;
        }
        .back-link:hover { color: #E60023; }
        .alert { border-radius: 8px; font-size: 14px; }
        .input-group-text { background: white; border-right: none; }
        .input-group .form-control { border-left: none; }
        .toggle-eye { cursor: pointer; user-select: none; }
    </style>
</head>
<body>

<div class="login-box">
    <div class="brand">
        <div class="logo">P</div>
        <div class="sub">Admin Portal</div>
    </div>

    <h2>Welcome back, Admin</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center py-2">
            Invalid email or password. Please try again.
        </div>
    <?php endif; ?>

    <form action="../controller/AdminController.php" method="POST">

        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter admin email" required autofocus>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <input type="password" name="password" id="adminPwd" class="form-control" placeholder="Enter password" required>
                <span class="input-group-text toggle-eye" onclick="togglePwd()">👁</span>
            </div>
        </div>

        <button type="submit" name="adminLogin" class="btn-login">
            Login to Dashboard
        </button>

    </form>

    <a href="../index.php" class="back-link">← Back to Home</a>
</div>

<script>
function togglePwd() {
    const f = document.getElementById('adminPwd');
    f.type = f.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>
