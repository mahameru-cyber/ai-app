<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

// ambil chat_id dari URL
$chat_id = isset($_GET['chat_id']) ? intval($_GET['chat_id']) : 0;

if (!$chat_id) {
    echo json_encode([]);
    exit;
}

// ambil pesan dari database
$stmt = $conn->prepare("SELECT role, content FROM messages WHERE chat_id=? ORDER BY id ASC");
if (!$stmt) {
    echo json_encode(["error" => "Prepare statement gagal"]);
    exit;
}
$stmt->bind_param("i", $chat_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "role" => $row['role'],
        "content" => $row['content']
    ];
}

// kirim JSON ke frontend
echo json_encode($data);

// 🔹 HAPUS penutup PHP