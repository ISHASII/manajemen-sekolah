<?php
require_once 'vendor/autoload.php';

echo "Testing time validation\n";

$start = \Carbon\Carbon::createFromFormat('H:i', '08:00');
$end = \Carbon\Carbon::createFromFormat('H:i', '09:00');

echo 'Start: ' . $start->format('H:i') . ', End: ' . $end->format('H:i') . ', Valid: ' . ($end->gt($start) ? 'Yes' : 'No') . "\n";

// Test invalid case
$start2 = \Carbon\Carbon::createFromFormat('H:i', '10:00');
$end2 = \Carbon\Carbon::createFromFormat('H:i', '09:00');

echo 'Start: ' . $start2->format('H:i') . ', End: ' . $end2->format('H:i') . ', Valid: ' . ($end2->gt($start2) ? 'Yes' : 'No') . "\n";
