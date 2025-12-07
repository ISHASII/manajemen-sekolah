<?php
$path = __DIR__ . '/../app/Http/Controllers/AdminController.php';
$lines = file($path);
$count = 0;
foreach ($lines as $i => $line) {
    $open = substr_count($line, '{');
    $close = substr_count($line, '}');
    $count += $open - $close;
    if ($count !== 0) {
        echo ($i + 1) . ':' . $count . "\n";
    }
}
echo 'final:' . $count . "\n";
