<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    if (empty($u) || empty($p)) {
        $error = "Username dan password wajib diisi!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $u);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($p, $user['password'])) {
                $_SESSION['user'] = $u;
                header("Location: index.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "User tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>
<link rel="icon" type="image/png" href="logo.png">
<link rel="stylesheet" href="assets/style.css">
</head>

<body class="auth-page">

<div class="auth-box">
  <h2>Login</h2>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- Form login biasa -->
  <form method="POST">
    <input name="username" placeholder="Username" required>
    <input name="password" type="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>

  <p>Atau login dengan:</p>
  <div style="display:flex; gap:10px; justify-content:center; margin-bottom:10px;">
      <a href="login-google.php" class="btn-google" style="padding:10px 15px; background:#DB4437; color:white; border-radius:6px; text-decoration:none; font-weight:bold;">Google</a>
      <a href="login-github.php" class="btn-github" style="padding:10px 15px; background:#333; color:white; border-radius:6px; text-decoration:none; font-weight:bold;">GitHub</a>
  </div>

  <p style="text-align:center;">
    Belum punya akun? <a href="register.php">Register</a>
  </p>
</div>

</body>
</html>