<?php declare(strict_types=1);

namespace Blog\Core\Content\Extension;

use Blog\Core\Content\Blog\Aggregate\BlogTranslationDefinition;
use Blog\Core\Content\BlogCategory\Aggregate\BlogCategoryTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\System\Language\LanguageDefinition;

class LanguageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'blogs',
                BlogTranslationDefinition::class,
                'language_id'
            )
        );

        $collection->add(
            new OneToManyAssociationField(
                'blogCategories',
                BlogCategoryTranslationDefinition::class,
                'language_id'
            )
        );
    }

    public function getDefinitionClass(): string
    {
        return LanguageDefinition::class;   
    }
}
