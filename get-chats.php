<?php
include 'db.php';
session_start();
header('Content-Type: application/json');

$user = $_SESSION['user'] ?? 'guest';

$stmt = $conn->prepare("SELECT id,title FROM chats WHERE user=? ORDER BY id DESC");
$stmt->bind_param("s",$user);
$stmt->execute();
$result = $stmt->get_result();

$data=[];
while($row=$result->fetch_assoc()){
    $data[]= ["id"=>$row['id'], "title"=>$row['title']];
}
echo json_encode($data);