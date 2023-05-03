<?php
// Crea un socket lato server.
$server_socket = stream_socket_server("tcp://0.0.0.0:10688", $errno, $errstr);

if (!$server_socket) {
    die("Impossibile creare il socket lato server: $errstr ($errno)\n");
}

// Loop infinito per accettare le connessioni in entrata.
while (true) {
    // Accetta la connessione in entrata da un client.
    $client_socket = stream_socket_accept($server_socket);

    if (!$client_socket) {
        die("Errore durante l'accettazione della connessione del client\n");
    }

    // Leggi i dati inviati dal client.
    $data = stream_get_contents($client_socket);
    print_r($data);
    // Memorizza i dati inviati dal client e la sua connessione.
    // Puoi utilizzare un array associativo per associare l'ID del client ai suoi dati e alla sua connessione.
    $client_id = uniqid();
    $clients[$client_id] = array(
        "data" => $data,
        "socket" => $client_socket
    );

    // Invia un messaggio di benvenuto al client.
    $welcome_message = "Benvenuto, client $client_id!\n";
    fwrite($client_socket, $welcome_message);

    // Chiudi la connessione con il client.
    fclose($client_socket);
}

// Chiudi il socket lato server.
fclose($server_socket);
