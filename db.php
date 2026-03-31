<?php
$conn = new mysqli("localhost", "root", 'Larsi*%$_#', "ai_app",3366);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>