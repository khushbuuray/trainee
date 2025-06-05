<?php declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Shopware\Core\Framework\Context;

interface CleanupHandlerInterface
{
    public function cleanup(array $config, bool $dryRun, Context $context): array;
    
    public function getName(): string;
}
