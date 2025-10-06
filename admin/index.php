<?php
// Start a session only if one isn't already active to prevent errors.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';

$error = '';
$login_success = false;

// If the admin is already logged in, redirect them to the dashboard.
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password_hash FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password_hash'])) {
            // Password is correct, set session variables and success flag
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $login_success = true; // Set flag for success message
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Shivarchanam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #9F0102;
            --accent-orange: #F58E58;
            --dark-text: #333333;
            --white: #ffffff;
            --body-bg: #f4f5f7;
            --success-green: #28a745;
            --danger-red: #dc3545;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #FFEFDA, var(--body-bg));
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }
        .login-card {
            max-width: 450px;
            width: 100%;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            padding: 3rem;
            text-align: center;
        }
        .temple-logo {
            width: 100px;
            height: 100px;
            margin-bottom: 1.5rem;
        }
        .login-card h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-red);
        }
        .login-card p {
            color: #666;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .input-wrapper {
            position: relative;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            width: 20px;
            height: 20px;
        }
        .password-toggle i {
            position: absolute;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .password-toggle .fa-eye-slash {
            opacity: 0;
        }
        .password-toggle.show .fa-eye {
            opacity: 0;
            transform: scale(0.7);
        }
        .password-toggle.show .fa-eye-slash {
            opacity: 1;
            transform: scale(1);
        }
        .btn-login {
            width: 100%;
            background: var(--primary-red);
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-login:hover {
            background-color: #7c0102;
        }
        
        /* Toast Notification Styles */
        .toast {
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            padding: 1rem 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            font-weight: 500;
            z-index: 1001;
            transition: top 0.5s ease-in-out;
        }
        .toast.show {
            top: 20px;
        }
        .toast.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .toast.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <img src="uploads/logo.png" alt="Temple Logo" class="temple-logo">
        <h2>Admin Panel</h2>
        <p>Please log in to continue.</p>

        <form action="index.php" method="post" id="login-form">
            <div class="form-group">
                <label for="username">Admin ID</label>
                <div class="input-wrapper">
                    <input type="text" id="username" name="username" value="admin" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required>
                    <span class="password-toggle" id="password-toggle">
                        <i class="fas fa-eye"></i>
                        <i class="fas fa-eye-slash"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle');

            if (toggleIcon) {
                toggleIcon.addEventListener('click', function() {
                    this.classList.toggle('show');
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                });
            }
            
            function showToast(message, type) {
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                toast.textContent = message;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);

                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 500);
                }, 3000); // Hide after 3 seconds
            }
            
            <?php if ($error): ?>
                showToast('<?php echo addslashes($error); ?>', 'error');
            <?php endif; ?>

            <?php if ($login_success): ?>
                // Show success message and then redirect
                showToast('Login Successful! Redirecting...', 'success');
                document.getElementById('login-form').style.display = 'none';
                setTimeout(function() {
                    window.location.href = 'dashboard.php';
                }, 1500); // Redirect after 1.5 seconds
            <?php endif; ?>
        });
    </script>
</body>
</html>

