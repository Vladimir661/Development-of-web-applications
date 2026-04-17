<?php
require_once '_db.php';

$stmt = $db->prepare("INSERT INTO rooms (name, capacity, status) VALUES (:name, :capacity, :status)");
$stmt->bindParam(':name', $_POST['name']);
$stmt->bindParam(':capacity', $_POST['capacity']);
$stmt->bindParam(':status', $_POST['status']);
$stmt->execute();

class Result {}
$response = new Result();
$response->result = 'OK';
$response->message = 'Room created';

header('Content-Type: application/json');
echo json_encode($response);
?>