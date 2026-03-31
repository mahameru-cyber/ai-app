<?php
session_start();
include 'db.php';
$apiKey = "MASUKKAN_API_KEY";

if ($_POST['type'] == 'chat') {
  $msg = $_POST['message'];

  $data = [
    "model" => "gpt-4.1-mini",
    "messages" => [["role"=>"user","content"=>$msg]]
  ];

  $ch = curl_init("https://api.openai.com/v1/chat/completions");
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>[
      "Content-Type: application/json",
      "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POSTFIELDS=>json_encode($data)
  ]);

  $res = json_decode(curl_exec($ch), true);
  $reply = $res['choices'][0]['message']['content'];

  $uid = $_SESSION['user']['id'];
  $conn->query("INSERT INTO chats(user_id,message,reply) VALUES('$uid','$msg','$reply')");

  echo json_encode(["reply"=>$reply]);
}

if ($_POST['type'] == 'image') {
  $prompt = $_POST['prompt'];

  $data = [
    "model" => "gpt-image-1",
    "prompt" => $prompt
  ];

  $ch = curl_init("https://api.openai.com/v1/images/generations");
  curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>[
      "Content-Type: application/json",
      "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POSTFIELDS=>json_encode($data)
  ]);

  $res = json_decode(curl_exec($ch), true);
  echo json_encode(["url"=>$res['data'][0]['url']]);
}
?>