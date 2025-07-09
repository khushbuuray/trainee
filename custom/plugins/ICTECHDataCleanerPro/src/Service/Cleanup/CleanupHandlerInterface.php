<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Service\Cleanup;

use Shopware\Core\Framework\Context;

interface CleanupHandlerInterface
{
    /**
     * @param array<string, mixed> $config
     * @return array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string}>}>}
     */
    public function cleanup(array $config, bool $dryRun, Context $context): array;

    public function getName(): string;

    /**
     * Returns the unique module key for identifying this handler (e.g. "productCleanup", "cartCleanup").
     */
    public function getKey(): string;
}
