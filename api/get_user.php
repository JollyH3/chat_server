<?php

require '../admin/function.php';

if (!isset($_GET['input'])) {

    $response = array('error' => 'Parametro "input" mancante.');
    echo json_encode($response);
    exit;
}

$input = trim($_GET['input']);

if (empty($input)) {

    $response = array('error' => 'Il valore dell\'input è vuoto.');
    echo json_encode($response);
    exit;
}

$input_parts = explode(" ", $input);
$name = $input_parts[0];
$surname = isset($input_parts[1]) ? $input_parts[1] : '';

$conn = sql_conn();

$name = $conn->real_escape_string($name);
$surname = $conn->real_escape_string($surname);

$query = "SELECT user_id, name, surname FROM user WHERE (name LIKE '%$name%' OR surname LIKE '%$name%') AND surname LIKE '%$surname%' LIMIT 5";

$result = $conn->query($query);

$users = array();

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($users);
?>
