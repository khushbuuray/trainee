<?php declare(strict_types=1);

namespace Example\Core\Content\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Language\LanguageDefinition;
use Shopware\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Example\Core\Content\Demo\DemoDefinition;

class CountryStateExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'demos',               
                DemoDefinition::class,
                'state_id'       // Foreign key in DemoDefinition
            )
        );
    }

    public function getDefinitionClass(): string
    {
        return CountryStateDefinition::class;
    }
}