<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/Database.php';
$db = Database::getInstance();
$mysqli = $db->getConnection();

if (isset($_POST['login'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        $error = "用户名或密码无效。";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录</title>
    <link rel="shortcut icon" href="/static/favicon.ico">
    <link rel="stylesheet" type="text/css" href="/static/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>登录</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="username">账号：</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码：</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="action-buttons">
                <button type="submit" name="login">登录</button>
            </div>
            <div class="reset-password">
                <a href="reset.php">重置密码</a>
            </div>
            <?php if (isset($error)) echo '<div id="error-message" style="display:none;">' . $error . '</div>'; ?>
        </form>
    </div>
    <script>
        function showNotification(message, className = 'green-success') {
            const existingNotification = document.querySelector('.green-success, .red-success');
            if (existingNotification) {
                existingNotification.parentNode.removeChild(existingNotification);
            }
            const notification = document.createElement('div');
            notification.classList.add(className);
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.classList.add('success-right');
                setTimeout(() => notification.parentNode.removeChild(notification), 1000);
            }, 1500);
        }
        
        const errorMessage = document.getElementById('error-message');
        if (errorMessage && errorMessage.textContent) {
            showNotification(errorMessage.textContent, 'red-success');
        }
    </script>
</body>
</html>