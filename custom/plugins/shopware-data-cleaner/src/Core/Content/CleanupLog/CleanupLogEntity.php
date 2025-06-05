<?php declare(strict_types=1);

namespace IctDataCleanerPro\Core\Content\CleanupLog;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CleanupLogEntity extends Entity
{
    use EntityIdTrait;

    protected \DateTimeInterface $runAt;
    protected string $trigger;
    protected string $mode;
    protected ?array $configSnapshot;
    protected ?array $results;

    public function getRunAt(): \DateTimeInterface
    {
        return $this->runAt;
    }

    public function setRunAt(\DateTimeInterface $runAt): void
    {
        $this->runAt = $runAt;
    }

    public function getTrigger(): string
    {
        return $this->trigger;
    }

    public function setTrigger(string $trigger): void
    {
        $this->trigger = $trigger;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getConfigSnapshot(): ?array
    {
        return $this->configSnapshot;
    }

    public function setConfigSnapshot(?array $configSnapshot): void
    {
        $this->configSnapshot = $configSnapshot;
    }

    public function getResults(): ?array
    {
        return $this->results;
    }

    public function setResults(?array $results): void
    {
        $this->results = $results;
    }
}
