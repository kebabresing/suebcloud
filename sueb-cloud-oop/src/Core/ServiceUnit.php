<?php

declare(strict_types=1);

namespace SuebCloud\Core;

/**
 * ServiceUnit menjadi parent class untuk seluruh layanan Sueb Cloud.
 * name/region/nodes/uptime dibuat protected agar anak dapat mewarisi tanpa
 * mengekspos langsung, sedangkan incidentLog private supaya hanya bisa
 * dimodifikasi lewat method pengendali.
 */
abstract class ServiceUnit
{
    protected string $serviceName;
    protected string $region;
    protected int $activeNodes;
    protected float $uptimePercent;
    private array $incidentLog = [];
    public string $serviceCode; // publik agar bisa diakses via object operator.

    public function __construct(string $serviceCode, string $serviceName, string $region, int $activeNodes = 1, float $uptimePercent = 99.9)
    {
        $this->serviceCode = $serviceCode;
        $this->serviceName = $serviceName;
        $this->region = $region;
        $this->activeNodes = max(1, $activeNodes);
        $this->uptimePercent = max(0.0, min(100.0, $uptimePercent));
    }

    public function __get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        throw new \InvalidArgumentException("Property {$key} tidak tersedia pada " . static::class);
    }

    public function __toString(): string
    {
        return sprintf('%s [%s] region %s — %.2f%% uptime', $this->serviceName, $this->serviceCode, $this->region, $this->uptimePercent);
    }

    protected function logIncident(string $message, string $severity): void
    {
        $this->incidentLog[] = [
            'message' => $message,
            'severity' => strtoupper($severity),
            'time' => date('c'),
        ];
    }

    public function assignTask(string $task): string
    {
        return $this->serviceName . " menangani tugas: " . $task . '.';
    }

    public function recordIncident(string $message, string $severity = 'low'): void
    {
        $this->logIncident($message, $severity);
        $this->uptimePercent = max(0.0, $this->uptimePercent - 0.05);
    }

    public function resolveIncident(string $message): string
    {
        $this->logIncident($message, 'RESOLVED');
        $this->uptimePercent = min(100.0, $this->uptimePercent + 0.02);
        return $this->serviceName . ' mengkonfirmasi insiden selesai: ' . $message;
    }

    public function scaleCapacity(int $nodes): void
    {
        $this->activeNodes = max(1, $this->activeNodes + $nodes);
        $this->uptimePercent = min(100.0, $this->uptimePercent + 0.01 * $nodes);
    }

    public function getStatusSnapshot(): array
    {
        return [
            'code' => $this->serviceCode,
            'name' => $this->serviceName,
            'region' => $this->region,
            'activeNodes' => $this->activeNodes,
            'uptimePercent' => $this->uptimePercent,
            'lastIncident' => end($this->incidentLog) ?: null,
            'incidents' => $this->incidentLog,
        ];
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getRegion(): string
    {
        return $this->region;
    }
}
