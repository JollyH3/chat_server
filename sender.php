<?php
require 'admin/function.php';


$serverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($serverSocket, "0.0.0.0", 10688);
socket_listen($serverSocket);


function handleClient($clientId, $clientSocket, &$activeConnections) {
    $currentTimestamp = time();

    while (true) {
        
        $data = socket_read($clientSocket, 1024);

        if (empty($data)) {
            // Rimuove la connessione dalla lista di connessioni attive.
            unset($activeConnections[$clientId]);
            break;
        }

        list($recipient_id, $sender_id, $message_text) = explode(" ", $data);

        // Se il destinatario è connesso, inoltra il messaggio.
        if (isset($activeConnections[$recipient_id])) {
            $recipientSocket = $activeConnections[$recipient_id]["socket"];
            socket_write($recipientSocket, $data, strlen($data));
            echo "Send data";
        } else {
            $pdo = sql_pdo();
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
            $pdo->query('set profiling=1');


            $stmt = $pdo->prepare("INSERT IGNORE INTO message VALUES(:sender_id, :recipient_id, :message_text);");
            if (!$stmt->execute([
                'sender_id' => $sender_id,
                'recipient_id' => $recipient_id,
                'message_text' => $message_text,
            ])) {
                $error = $stmt->errorInfo();
                error_log("Errore durante l'esecuzione della query SQL: " . $error[2]);
            } else {
                echo "Saved data: Sender: $sender_id, Recipient: $recipient_id, message: $message_text\n";
            }
        }
    }

    socket_close($clientSocket);

    if ($currentTimestamp % 10 == 0) {
        echo "Connessioni attive:\n";
        foreach ($activeConnections as $clientId => $connection) {
            $connectionTime = $currentTimestamp - $connection["timestamp"];
            echo "- $clientId (da $connectionTime secondi)\n";
        }
    }
}

$activeConnections = array();
$currentTimestamp = time();


while (true) {

    $newClientSocket = socket_accept($serverSocket);

    $clientId = socket_read($newClientSocket, 1024);

    // Aggiunge la connessione alla lista di connessioni attive.
    $activeConnections[$clientId] = array(
        "socket" => $newClientSocket,
        "timestamp" => $currentTimestamp
    );

    //thread per gestire il client.
    $pid = pcntl_fork();
    if ($pid == -1) {
        die('Impossibile creare un nuovo thread');
    } elseif ($pid) {
        // Processo padre: continua ad accettare connessioni.
        continue;
    } else {
        // Processo filgio: gestisce il client.
        handleClient($clientId, $newClientSocket, $activeConnections);

        socket_shutdown($newClientSocket, 2);
        socket_close($newClientSocket);
        exit();
    }
}

socket_close($serverSocket);
