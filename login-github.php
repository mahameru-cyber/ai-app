<?php
session_start();
$clientID = 'YOUR_GITHUB_CLIENT_ID';
$redirectURI = 'https://yourdomain.com/login-github-callback.php';
$githubAuthUrl = "https://github.com/login/oauth/authorize?client_id={$clientID}&redirect_uri={$redirectURI}&scope=user";
header("Location: $githubAuthUrl");
exit;