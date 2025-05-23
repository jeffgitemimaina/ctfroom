<?php
function checkRateLimit($action, $ip, $limit = 10, $window = 60) {
    $cacheDir = __DIR__ . '/logs/rate_limit/';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }
    $file = $cacheDir . md5($action . $ip) . '.txt';
    $now = time();

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data['time'] > $now - $window) {
            if ($data['count'] >= $limit) {
                return false; // Rate limit exceeded
            }
            $data['count']++;
        } else {
            $data = ['count' => 1, 'time' => $now];
        }
    } else {
        $data = ['count' => 1, 'time' => $now];
    }

    file_put_contents($file, json_encode($data));
    return true;
}
?>