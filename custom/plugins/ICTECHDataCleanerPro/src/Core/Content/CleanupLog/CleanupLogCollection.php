<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Core\Content\CleanupLog;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<CleanupLogEntity>
 *
 * @method void add(CleanupLogEntity $entity)
 * @method void set(string $key, CleanupLogEntity $entity)
 * @method CleanupLogEntity[] getIterator()
 * @method CleanupLogEntity[] getElements()
 * @method CleanupLogEntity|null get(string $key)
 * @method CleanupLogEntity|null first()
 * @method CleanupLogEntity|null last()
 */
class CleanupLogCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CleanupLogEntity::class;
    }
}