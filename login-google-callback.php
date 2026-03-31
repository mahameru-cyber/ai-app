<?php
require_once 'vendor/autoload.php';
session_start();
include 'db.php';

$client = new Google_Client();
$client->setClientId(getenv('GOOGLE_CLIENT_ID'));
$client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
$client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));

$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {

    // Ambil token dari Google
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        echo "Error Token: " . $token['error'];
        exit;
    }

    $client->setAccessToken($token);

    // Ambil data user
    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    $email = $userInfo->email;
    $name  = $userInfo->name;

    // Cek user di database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Kalau belum ada → insert user baru
        $passwordDummy = password_hash("google_login", PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $passwordDummy);
        $stmt->execute();
    }

    // Set session login
    $_SESSION['user'] = $email;
    $_SESSION['name'] = $name;

    // Redirect ke dashboard
    header('Location: index.php');
    exit;

} else {
    echo "No code received!";
}