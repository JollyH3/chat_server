<?php

$address = "0.0.0.0";
$port = 10688;
$null = NULL;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($sock, $address, $port);
socket_listen($sock);

echo "Listening for new connections on port {$port} \n";

$members = [];
$connections = [];

$connections[] = $sock;

while(true){
    $reads = $connections;
    $write = $exceptions = $null;

    socket_select($reads, $write, $exceptions, 0);

    if(in_array($sock, $reads)) {
        $new_connections = socket_accept($sock);
        $connections[] = $new_connections;
        $reply = "conected to the chat socket server \n";
        socket_write($new_connections, $reply, strlen($reply));

        $socket_index = array_search($sock, $reads);
        unset($reads[$socket_index]);
    }

    foreach($reads as $key => $value) {
        $data = socket_read($value, 1024);

        if(!empty($data)){
            //write to all clients
            foreach($connections as $ckey => $cvalue){
                if($ckey === 0) continue;
                socket_write($cvalue, $data, strlen($data));
            }
        }
        elseif ($data === ''){

            echo "disconnecting client $key \n";
            unset($connections[$key]);
            socket_close($value);
        }
    }
}

socket_close($sock);