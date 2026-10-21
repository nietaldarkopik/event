<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$events = App\Models\Event::all();
echo "Available Events:\n";
foreach ($events as $event) {
    echo "- [ID: " . $event->id . "] " . $event->title .
        " (" . $event->event_type . ") - " .
        ($event->start_datetime ? $event->start_datetime->format('Y-m-d H:i') : 'No Date') . "\n";
}
echo "\nTotal: " . $events->count() . "\n";
