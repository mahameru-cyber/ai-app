<?php
session_start();
include 'db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $p2 = $_POST['password2'] ?? '';

    if (empty($u) || empty($p) || empty($p2)) {
        $error = "Semua field wajib diisi!";
    } elseif ($p !== $p2) {
        $error = "Password dan konfirmasi tidak sama!";
    } else {
        // Cek username sudah ada
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $u);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            // Insert user baru
            $hash = password_hash($p, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $u, $hash);
            if ($stmt->execute()) {
                $_SESSION['user'] = $u;
                $success = "Registrasi berhasil! Redirecting...";
                header("Refresh:1; url=index.php");
            } else {
                $error = "Terjadi kesalahan, coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register</title>
<link rel="stylesheet" href="assets/style.css">
</head>

<body class="auth-page">

<div class="auth-box">
  <h2>Register</h2>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <!-- Form register biasa -->
  <form method="POST">
    <input name="username" placeholder="Username" required>
    <input name="password" type="password" placeholder="Password" required>
    <input name="password2" type="password" placeholder="Konfirmasi Password" required>
    <button type="submit">Register</button>
  </form>

  <p>Atau daftar/login dengan:</p>
  <div style="display:flex; gap:10px; justify-content:center; margin-bottom:10px;">
      <a href="login-google.php" class="btn-google" style="padding:10px 15px; background:#DB4437; color:white; border-radius:6px; text-decoration:none; font-weight:bold;">Google</a>
      <a href="login-github.php" class="btn-github" style="padding:10px 15px; background:#333; color:white; border-radius:6px; text-decoration:none; font-weight:bold;">GitHub</a>
  </div>

  <p style="text-align:center;">
    Sudah punya akun? <a href="login.php">Login</a>
  </p>
</div>

</body>
</html>