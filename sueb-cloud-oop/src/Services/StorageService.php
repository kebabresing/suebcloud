<?php

declare(strict_types=1);

namespace SuebCloud\Services;

use SuebCloud\Core\ServiceUnit;

class StorageService extends ServiceUnit
{
    private int $redundancyLevel;
    private array $bucketPolicies;

    public function __construct(string $serviceCode, string $region, int $redundancyLevel = 3, array $bucketPolicies = [])
    {
        parent::__construct($serviceCode, 'Object Storage', $region, 2 * $redundancyLevel, 99.99);
        $this->redundancyLevel = max(2, $redundancyLevel);
        $this->bucketPolicies = $bucketPolicies ?: ['public-read' => false];
    }

    public function replicateBucket(string $bucket, string $targetRegion): string
    {
        $this->recordIncident('Replicating ' . $bucket . ' ke ' . $targetRegion, 'info');
        return sprintf('Bucket %s direplikasi ke %s untuk redundansi level %d.', $bucket, $targetRegion, $this->redundancyLevel);
    }

    public function updatePolicy(string $policy, bool $enabled): void
    {
        $this->bucketPolicies[$policy] = $enabled;
        if ($enabled) {
            $this->scaleCapacity(1);
        }
    }
}
