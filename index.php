<?php
session_start();
$isLogin = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Chat</title>
<link rel="icon" type="image/png" href="logo.png">

<link rel="stylesheet" href="assets/style.css">

<!-- Highlight.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
</head>
<body>

<div class="app">

  <?php if ($isLogin): ?>
  <!-- SIDEBAR -->
  <div class="sidebar">
    <div class="sidebar-top">
      <button class="new-chat" onclick="createChat()">+ New Chat</button>
    </div>

    <div id="chat-list" class="chat-list"></div>

    <div class="sidebar-bottom">
      <div class="user-box">
        <span>👤 <?= htmlspecialchars($_SESSION['user']) ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="main <?php echo $isLogin ? 'chat-mode' : 'center-mode'; ?>">

    <?php if (!$isLogin): ?>
    <!-- TOP BAR GUEST -->
    <div class="top-bar">
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    </div>

    <div class="center-box">
      <div class="welcome">Welcome to AI Chat</div>

      <div class="auth-box">
        <!-- FORM LOGIN -->
        <form method="post" action="login.php">
          <input type="text" name="username" placeholder="Username" required>
          <input type="password" name="password" placeholder="Password" required>
          <button type="submit">Login</button>
        </form>

        <p style="margin:10px 0;">Or login with</p>
        <div style="display:flex; gap:10px; justify-content:center; margin-bottom:10px;">
          <a href="login-google.php" style="padding:10px 15px; border-radius:6px; background:#DB4437; color:white; text-decoration:none; font-weight:bold;">Google</a>
          <a href="login-github.php" style="padding:10px 15px; border-radius:6px; background:#333; color:white; text-decoration:none; font-weight:bold;">GitHub</a>
        </div>

        

        <p style="text-align:center;">Don't have an account? <a href="register.php">Register</a></p>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($isLogin): ?>
    <!-- CHAT BOX -->
    <div id="chat-box" class="chat-box"></div>

    <!-- FLOATING INPUT CENTER -->
    <div class="input-wrapper">
      <div class="input-box">
        <textarea id="msg" rows="1" placeholder="Ask anything..."></textarea>
        <button id="sendBtn" onclick="send()" disabled>➤</button>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<script src="assets/script.js"></script>
</body>
</html>