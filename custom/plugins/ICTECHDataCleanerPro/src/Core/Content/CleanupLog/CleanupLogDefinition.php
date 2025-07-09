<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Core\Content\CleanupLog;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CleanupLogDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'ict_data_cleanup_log';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CleanupLogEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CleanupLogCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new DateTimeField('run_at', 'runAt'))->addFlags(new Required()),
            (new StringField('trigger', 'trigger'))->addFlags(new Required()),
            (new StringField('mode', 'mode'))->addFlags(new Required()),
            new JsonField('config_snapshot', 'configSnapshot'),
            new JsonField('results', 'results'),
        ]);
    }
}
