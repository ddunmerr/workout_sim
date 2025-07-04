<?php
date_default_timezone_set("UTC");
$logFile = 'debug_log.txt';
$distanceFile = 'distance_value.txt';

// Логирование
file_put_contents($logFile, date('c') . " | GET | distance\n", FILE_APPEND);

// Получаем предыдущее значение
$distance = 0;
if (file_exists($distanceFile)) {
    $distance = file_get_contents($distanceFile);
}

// Увеличиваем значение (например, на 0.1)
$distance += 1;

// Сохраняем обратно
file_put_contents($distanceFile, $distance);

// Возвращаем JSON-ответ
header('Content-Type: application/json');
echo json_encode(['distance' => $distance]);
