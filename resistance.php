<?php
date_default_timezone_set("UTC");
$logFile = 'debug_log.txt';
$storageFile = 'resistance_value.txt';

// Получаем метод
$method = $_SERVER['REQUEST_METHOD'];

// Логируем
$body = file_get_contents('php://input');
file_put_contents($logFile, date('c') . " | $method | $body\n", FILE_APPEND);

// Если POST – сохраняем значение
if ($method === 'POST') {
    $data = json_decode($body, true);
    if (isset($data['resistance'])) {
        file_put_contents($storageFile, $data['resistance']);
    }
}

// Читаем текущее значение
$resistance = file_exists($storageFile) ? file_get_contents($storageFile) : 0;

// Показываем его на странице
?>
<!DOCTYPE html>
<html>

<head>
    <title>Resistance Debug</title>
</head>

<body>
    <h1>Сопротивление: <?= htmlspecialchars($resistance) ?></h1>
</body>

</html>