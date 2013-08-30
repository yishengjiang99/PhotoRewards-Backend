
<?php

$socket = stream_socket_server("udp://ec2-54-243-83-219.compute-1.amazonaws.com:1337", $errno, $errstr, STREAM_SERVER_BIND);
if (!$socket) {
    die("$errstr ($errno)");
}

do {
    $pkt = stream_socket_recvfrom($socket, 4, 0, $peer);
    file_put_contents("/var/www/battery_level","\n".time()." $pkt",FILE_APPEND);
	echo $pkt;
} while ($pkt !== false);

?>







