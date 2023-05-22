<?php
require '../admin/function.php';

$name = json_decode(file_get_contents('php://input'), true)['name'];
$surname = json_decode(file_get_contents('php://input'), true)['surname'];

if(empty($name) || empty($surname)) {
    http_response_code(400);
    echo json_encode(array('error' => 'I campi name e surname sono obbligatori'));
    exit;
}

$pdo = sql_pdo();
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
$pdo->query('set profiling=1');

$client_id = hash_hmac("sha256", uniqid(), "user_id");
$stmt = $pdo->prepare("INSERT IGNORE INTO user VALUES(:user_id, :name, :surname, :token);");
if($stmt->execute([
    'user_id' => $client_id,
    'name' => $name,
    'surname' => $surname,
    'token' => hash_hmac("sha256", uniqid(), "token"),
])) {
    http_response_code(201);
    echo json_encode(array('error' => '0', 'client_id' => $client_id));
} else {
    http_response_code(500);
    echo json_encode(array('error' => '1'));
}
