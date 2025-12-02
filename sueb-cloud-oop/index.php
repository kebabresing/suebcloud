<?php

declare(strict_types=1);

$baseDir = __DIR__ . '/src/';

spl_autoload_register(static function (string $class) use ($baseDir): void {
    $prefix = 'SuebCloud\\';
    $isInNamespace = strncmp($class, $prefix, strlen($prefix)) === 0;

    if (!$isInNamespace) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

    if (is_file($path)) {
        require_once $path;
    }
});

use SuebCloud\Services\ComputeService;
use SuebCloud\Services\StorageService;

$compute = new ComputeService('SC-CPT', 'Jakarta', ['nebula-a' => 5, 'nova-b' => 3]);
$storage = new StorageService('SC-OBJ', 'Surabaya', 4);

$taskFeed = [
    $compute->assignTask('Scale up traffic lebaran'),
    $storage->assignTask('Snapshot harian customer enterprise'),
];

$events = [
    $compute->deployCluster('nebula-a', 2),
    $compute->toggleAutoHealing(false),
    $storage->replicateBucket('customer-artifacts', 'Singapore'),
    (function () use ($storage) {
        $storage->updatePolicy('zero-trust', true);
        return 'Storage policy zero-trust diaktifkan.';
    })(),
    $storage->resolveIncident('Replikasi selesai'),
];

$snapshots = [
    'compute' => $compute->getStatusSnapshot(),
    'storage' => $storage->getStatusSnapshot(),
];

$divider = str_repeat('=', 60);

echo $divider . PHP_EOL;
echo 'Sueb Cloud Mission Console (CLI Edition)' . PHP_EOL;
echo $divider . PHP_EOL . PHP_EOL;

foreach ([
    'Compute Fabric' => [$compute, $snapshots['compute']],
    'Object Storage' => [$storage, $snapshots['storage']],
] as $label => [$service, $snapshot]) {
    echo $label . PHP_EOL;
    echo str_repeat('-', strlen($label)) . PHP_EOL;
    echo 'Name   : ' . $service->getServiceName() . PHP_EOL;
    echo 'Code   : ' . $service->serviceCode . PHP_EOL;
    echo 'Region : ' . $service->getRegion() . PHP_EOL;
    echo 'Nodes  : ' . $snapshot['activeNodes'] . PHP_EOL;
    echo 'Uptime : ' . number_format($snapshot['uptimePercent'], 2) . " %" . PHP_EOL;
    echo 'String : ' . $service . PHP_EOL . PHP_EOL;
}

echo "Antrian Penugasan\n";
echo "-------------------\n";
foreach ($taskFeed as $index => $task) {
    echo ($index + 1) . '. ' . $task . PHP_EOL;
}

echo PHP_EOL . "Timeline Operasi\n";
echo "----------------\n";
foreach ($events as $index => $event) {
    echo ($index + 1) . '. ' . $event . PHP_EOL;
}

echo PHP_EOL;
echo 'Laporan dibuat pada: ' . date('c') . PHP_EOL;
echo $divider . PHP_EOL;
