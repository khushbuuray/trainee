<?php declare(strict_types=1);

namespace Example\Core\Content\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Content\Media\MediaDefinition;
use Example\Core\Content\Demo\DemoDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;


class MediaExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'demos',
                DemoDefinition::class,
                'media_id'
            )
        );
    }

    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}