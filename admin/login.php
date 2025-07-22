<?php
require_once '../includes/config.php';
require_once '../includes/security.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login_admin($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
        log_security_event('Failed admin login attempt', "Username: $username, IP: " . get_client_ip());
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1>Admin Login</h1>
                <p><?php echo SITE_NAME; ?> Administration</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="contact-form">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="form-button">Login</button>
                </div>
            </form>
            
            <div class="back-link">
                <a href="/">← Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html> 