<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

$message = json_decode(file_get_contents("php://input"), true)['message'] ?? '';
$chat_id = json_decode(file_get_contents("php://input"), true)['chat_id'] ?? 0;
$user = $_SESSION['user'] ?? 'guest';

if (!$message) { echo json_encode(["reply"=>"Pesan kosong"]); exit; }

// 🔑 OpenRouter API key
$apiKey = "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";

// buat chat baru jika belum ada
if (!$chat_id) {
    $title = substr($message,0,30);
    $stmt = $conn->prepare("INSERT INTO chats (user,title) VALUES (?,?)");
    $stmt->bind_param("ss",$user,$title);
    $stmt->execute();
    $chat_id = $conn->insert_id;
}

// simpan user message
$stmt = $conn->prepare("INSERT INTO messages (chat_id,role,content) VALUES (?,?,?)");
$role="user";
$stmt->bind_param("iss",$chat_id,$role,$message);
$stmt->execute();

// request AI
$postData = ["model"=>"openai/gpt-3.5-turbo","messages"=>[["role"=>"user","content"=>$message]]];
$ch = curl_init();
curl_setopt_array($ch,[
    CURLOPT_URL=>"https://openrouter.ai/api/v1/chat/completions",
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>json_encode($postData),
    CURLOPT_HTTPHEADER=>["Authorization: Bearer ".$apiKey,"Content-Type: application/json"],
    CURLOPT_TIMEOUT=>10
]);
$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

// fallback AI
$reply = '';
if ($curlError) {
    $reply = "AI fallback: ".$message;
} else {
    $result = json_decode($response,true);
    if (isset($result['error']) || !isset($result['choices'][0]['message']['content'])) {
        $reply = "AI fallback: ".$message;
    } else {
        $reply = $result['choices'][0]['message']['content'];
    }
}

// simpan AI message ke DB
$stmt = $conn->prepare("INSERT INTO messages (chat_id,role,content) VALUES (?,?,?)");
$role="ai";
$stmt->bind_param("iss",$chat_id,$role,$reply);
$stmt->execute();

// kirim ke frontend
echo json_encode(["reply"=>$reply,"chat_id"=>$chat_id]);