<?php

declare(strict_types=1);

namespace SuebCloud\Services;

use SuebCloud\Core\ServiceUnit;

class ComputeService extends ServiceUnit
{
    private array $vmPools;
    private bool $autoHealing;

    public function __construct(string $serviceCode, string $region, array $vmPools, bool $autoHealing = true)
    {
        parent::__construct($serviceCode, 'Compute Fabric', $region, count($vmPools), 99.95);
        $this->vmPools = $vmPools;
        $this->autoHealing = $autoHealing;
    }

    public function deployCluster(string $pool, int $instances): string
    {
        $this->scaleCapacity($instances);
        $this->vmPools[$pool] = ($this->vmPools[$pool] ?? 0) + $instances;
        return sprintf('Cluster %s ditambah %d VM pada %s.', $pool, $instances, $this->region);
    }

    public function toggleAutoHealing(bool $enabled): string
    {
        $this->autoHealing = $enabled;
        return 'Auto-healing compute di ' . ($enabled ? 'aktifkan.' : 'matikan.');
    }
}
