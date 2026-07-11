<?php
/**
 * Metrica Polls - Staging/Deployment Log Diagnoser
 */
$logPath = __DIR__ . '/storage/logs/laravel.log';
header('Content-Type: text/plain; charset=utf-8');

if (!file_exists($logPath)) {
    echo "Laravel log file not found at: " . $logPath . "\n";
    echo "This usually indicates the application crashed before the logger booted.\n";
    exit;
}

$content = file_get_contents($logPath);
$lines = explode("\n", $content);
$lastLines = array_slice($lines, -150);

echo "--- LAST 150 LINES OF LARAVEL LOG ---\n\n";
echo implode("\n", $lastLines);
