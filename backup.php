<?php
// index.php — эмуляция тренажёра с Wi-Fi/HTTP API

date_default_timezone_set("UTC");

// Файл для хранения состояния устройства
$stateFile = __DIR__ . '/device_state.json';

// Загрузка состояния, либо инициализация
if (file_exists($stateFile)) {
    $state = json_decode(file_get_contents($stateFile), true);
} else {
    $state = [
        'resistance'   => 0,
        'distance'     => 0.0,
        'running'      => false,
        'last_update'  => time()
    ];
}

// Обновление дистанции, если тренажёр работает
$now = time();
if ($state['running']) {
    $elapsed = $now - $state['last_update'];
    if ($elapsed > 0) {
        $speed = max(0, $state['resistance']) * 0.1;
        $state['distance'] += $speed * $elapsed;
    }
}
$state['last_update'] = $now;

// Обработка POST-запросов
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($uri === '/start') {
        $state['running'] = true;
    } elseif ($uri === '/stop') {
        $state['running'] = false;
    } elseif ($uri === '/reset') {
        $state['running']     = false;
        $state['distance']    = 0.0;
        $state['resistance']  = 0;
    } elseif ($uri === '/' || $uri === '') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['resistance'])) {
            $state['resistance'] = max(0, (int)$data['resistance']);
        }
    }

    file_put_contents($stateFile, json_encode($state));
    http_response_code(204);
    exit;
}

// Обработка GET-запросов
if (isset($_GET['json'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'resistance' => $state['resistance'],
        'distance'   => round($state['distance'], 2),
        'running'    => $state['running']
    ]);
    file_put_contents($stateFile, json_encode($state));
    exit;
}
