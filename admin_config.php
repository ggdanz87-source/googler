php
<?php
// admin_config.php - Panel untuk ganti email admin
session_start();

// Default admin login (bisa diubah)
$ADMIN_USER = "zerozx";
$ADMIN_PASS = "admin123";

// Cek login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    if ($_POST['username'] == $ADMIN_USER && $_POST['password'] == $ADMIN_PASS) {
        $_SESSION['admin_auth'] = true;
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: admin_config.php');
    exit();
}

// Proses ganti email
if (isset($_POST['save_email']) && isset($_SESSION['admin_auth'])) {
    $new_email = trim($_POST['admin_email']);
    if (filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        // Simpan email ke file
        file_put_contents('admin_email.conf', $new_email);
        $success = "Email admin berhasil diubah menjadi: $new_email";
    } else {
        $error = "Email tidak valid!";
    }
}

// Baca email saat ini
$current_email = file_exists('admin_email.conf') ? file_get_contents('admin_email.conf') : 'admin@zerozx.com';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Config - Zerozx System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #0a0f1e 0%, #000 100%);
            font-family: 'Courier New', monospace;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: rgba(0,0,0,0.85);
            border: 1px solid #00ff88;
            border-radius: 20px;
            padding: 40px;
            width: 90%;
            max-width: 500px;
        }
        h1 { color: #00ff88; text-align: center; margin-bottom: 20px; }
        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #111;
            border: 1px solid #333;
            border-radius: 8px;
            color: #00ff88;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #00ff8822;
            border: 1px solid #00ff88;
            color: #00ff88;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover { background: #00ff88; color: #000; }
        .success { color: #00ff88; margin: 10px 0; }
        .error { color: #ff4444; margin: 10px 0; }
        .current-email {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }
        hr { border-color: #333; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚙️ ADMIN CONFIG</h1>
        
        <?php if (!isset($_SESSION['admin_auth'])): ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">🔐 Login Admin</button>
            </form>
            <div style="margin-top: 15px; font-size: 12px; color: #555; text-align: center;">
                Default: zerozx / admin123
            </div>
        <?php else: ?>
            <div class="current-email">
                📧 Email Admin Saat Ini: <strong style="color:#00ff88"><?= htmlspecialchars($current_email) ?></strong>
            </div>
            
            <?php if(isset($success)): ?>
                <div class="success">✅ <?= $success ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="error">❌ <?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="email" name="admin_email" placeholder="Email Admin Baru" value="<?= htmlspecialchars($current_email) ?>" required>
                <button type="submit" name="save_email">💾 Ganti Email Admin</button>
            </form>
            
            <hr>
            
            <form method="POST">
                <button type="submit" name="logout" style="background:#ff444422; border-color:#ff4444; color:#ff4444;">🚪 Logout</button>
            </form>
            
            <div style="margin-top: 20px; font-size: 12px; color: #555; text-align: center;">
                ⚠️ Setelah ganti email, semua data akan dikirim ke email baru
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
