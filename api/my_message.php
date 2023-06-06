<?php
require '../admin/function.php';

$user_id = $_POST['client_id'];

$conn = sql_conn();

$sql = "SELECT * FROM message WHERE recipient_id = '$user_id'";
$result = $conn->query($sql);

$messages = array();

if ($result->num_rows > 0){

    while ($row = $result->fetch_assoc()){

        $message = array(
            'id' => $row['id'],
            'sender_id' => $row['sender_id'],
            'recipient_id' => $row['recipient_id'],
            'text' => $row['text'],
        );

        $messages[] = $message;

        $message_id = $row['id'];
        $delete_sql = "DELETE FROM message WHERE id = '$message_id'";
        $conn->query($delete_sql);

    }

    $conn->close();

    // Restituisci i messaggi come risposta JSON
    header('Content-Type: application/json');
    echo json_encode($messages);
} else {
    // Restituisci una risposta vuota come JSON
    header('Content-Type: application/json');
    echo json_encode(array('message' => 'No message'));
}
