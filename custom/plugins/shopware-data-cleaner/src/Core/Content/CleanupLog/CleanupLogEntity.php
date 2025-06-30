<?php declare(strict_types=1);

namespace IctDataCleanerPro\Core\Content\CleanupLog;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

/**
 * @phpstan-type ConfigSnapshot array<string, mixed>
 * @phpstan-type Results array<string, array<string, mixed>>
 */
class CleanupLogEntity extends Entity
{
    use EntityIdTrait;

    protected \DateTimeInterface $runAt;
    protected string $trigger;
    protected string $mode;

    /** @var array<string, mixed>|null */
    protected ?array $configSnapshot = null;

    /** @var array<string, array<string, mixed>>|null */
    protected ?array $results = null;

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

    /**
     * @return array<string, mixed>|null
     */
    public function getConfigSnapshot(): ?array
    {
        return $this->configSnapshot;
    }

    /**
     * @param array<string, mixed>|null $configSnapshot
     */
    public function setConfigSnapshot(?array $configSnapshot): void
    {
        $this->configSnapshot = $configSnapshot;
    }

    /**
     * @return array<string, array<string, mixed>>|null
     */
    public function getResults(): ?array
    {
        return $this->results;
    }

    /**
     * @param array<string, array<string, mixed>>|null $results
     */
    public function setResults(?array $results): void
    {
        $this->results = $results;
    }
}
