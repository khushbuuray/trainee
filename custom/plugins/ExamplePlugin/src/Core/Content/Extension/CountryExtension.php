<?php declare(strict_types=1);

namespace Example\Core\Content\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Country\CountryDefinition;
use Example\Core\Content\Demo\DemoDefinition;
class CountryExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
          new OneToManyAssociationField(
            'demos',
            DemoDefinition::class,
            'country_id'
          )
        );
    }

    public function getDefinitionClass(): string
    {
        return CountryDefinition::class;
    }


}