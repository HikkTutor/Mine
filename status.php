<?php
header('Content-Type: application/json');
$ip = $_GET['ip'] ?? '88.151.117.88';
$port = (int)($_GET['port'] ?? 25622);

// Таймаут 2 секунды
$socket = @fsockopen("udp://$ip", $port, $errno, $errstr, 2);

if (!$socket) {
    die(json_encode(['online' => false]));
}

// Отправляем пакет для Bedrock (Magic Bytes)
fwrite($socket, "\x01\x00\x00\x00\x00\x00\x00\x00\x00\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78");
stream_set_timeout($socket, 2);
$response = fread($socket, 4096);
fclose($socket);

if (empty($response)) {
    die(json_encode(['online' => false]));
}

// Парсим ответ Bedrock
$data = explode(';', $response);
echo json_encode([
    'online' => true,
    'players' => $data[4] ?? null,
    'maxPlayers' => $data[5] ?? null
]);
?>
