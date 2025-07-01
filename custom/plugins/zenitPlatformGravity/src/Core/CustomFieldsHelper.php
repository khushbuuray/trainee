<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Core;

use Exception;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Cms\CmsPageDefinition;
use Shopware\Core\Content\LandingPage\LandingPageDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\CustomField\CustomFieldTypes;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\Context;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CustomFieldsHelper {

    private EntityRepository $customFieldRepository;
    private EntityRepository $customFieldSetRepository;


    private const THEME_INSTANCE = 'gravity';

    public function __construct(EntityRepository $customFieldRepository, EntityRepository $customFieldSetRepository){
        $this->customFieldRepository = $customFieldRepository;
        $this->customFieldSetRepository = $customFieldSetRepository;
    }

    /**
     * Method to get the customfields.
     *
     * @param ContainerInterface $container
     * @param Context $context
     *
     */


    public function getCustomFields($container, $context): void
    {
        $relationCms = [
            'id' => Uuid::randomHex(),
            'entityName' =>  $container->get(CmsPageDefinition::class)->getEntityName()
        ];

        $relationLanding1 = [
            'id' => Uuid::randomHex(),
            'entityName' =>  $container->get(LandingPageDefinition::class)->getEntityName()
        ];

        $relationCategory1 = [
            'id' => Uuid::randomHex(),
            'entityName' =>  $container->get(CategoryDefinition::class)->getEntityName()
        ];

        $relationCategory2 = [
            'id' => Uuid::randomHex(),
            'entityName' =>  $container->get(CategoryDefinition::class)->getEntityName()
        ];

        $relationCategory3 = [
            'id' => Uuid::randomHex(),
            'entityName' =>  $container->get(CategoryDefinition::class)->getEntityName()
        ];

        $relationCategory4 = [
            'id' => Uuid::randomHex(),
            'entityName' =>  $container->get(CategoryDefinition::class)->getEntityName()
        ];

        $relationProduct1 = [
            'id' => Uuid::randomHex(),
            'entityName' =>  $container->get(ProductDefinition::class)->getEntityName()
        ];

        $relationProduct2 = [
            'id' => Uuid::randomHex(),
            'entityName' =>  $container->get(ProductDefinition::class)->getEntityName()
        ];

        $relationProduct3 = [
            'id' => Uuid::randomHex(),
            'entityName' =>  $container->get(ProductDefinition::class)->getEntityName()
        ];

        $customCmsFields = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_grid_gap',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'customFieldType' => 'text',
                    'customFieldPosition' => 10,
                    'label' => [
                        'en-GB' => 'Grid gap',
                        'de-DE' => 'Abstand zwischen Kacheln',
                    ]
                ]
            ],
        ];

        $customCategoryFields = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_classes',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'customFieldType' => 'text',
                    'customFieldPosition' => 20,
                    'label' => [
                        'en-GB' => 'CSS classes',
                        'de-DE' => 'CSS-Klassen',
                    ],
                    'helpText' => [
                        'en-GB' => 'Multiple classes can be added by separating them with a space. No dots before class names.',
                        'de-DE' => 'Mehrere Klassen können mit einem Leerzeichen getrennt hinzugefügt werden. Keine Punkte vor den Klassennamen.',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_badge_text',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'customFieldType' => 'text',
                    'customFieldPosition' => 30,
                    'label' => [
                        'en-GB' => 'Badge - text',
                        'de-DE' => 'Label - Text',
                    ],
                    'placeholder' => [
                        'en-GB' => 'e.g. New, Top',
                        'de-DE' => 'z.B. Neu, Top',
                    ],
                    'helpText' => [
                        'en-GB' => 'Displays a badge in main navigation.',
                        'de-DE' => 'Zeigt ein Label im Menü an.',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_badge_bg',
                'type' => CustomFieldTypes::COLORPICKER,
                'config' => [
                    'componentName' => 'sw-field',
                    'customFieldType' => 'colorpicker',
                    'customFieldPosition' => 40,
                    'label' => [
                        'en-GB' => 'Badge - background color',
                        'de-DE' => 'Label - Hintergrundfarbe',
                    ],
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_badge_color',
                'type' => CustomFieldTypes::COLORPICKER,
                'config' => [
                    'componentName' => 'sw-field',
                    'customFieldType' => 'colorpicker',
                    'customFieldPosition' => 50,
                    'label' => [
                        'en-GB' => 'Badge - text color',
                        'de-DE' => 'Label - Textfarbe',
                    ],
                ],
            ],
        ];

        $productListingColumnsCategoryFields = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_product_listing_columns_xs',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 10,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Small smartphones',
                        'de-DE' => 'Kleine Smartphones',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Number of columns per row. Indicates how many articles are displayed in one line in the selected viewport.',
                        'de-DE' => 'Anzahl der Spalten pro Zeile. Gibt an, wie viele Artikel in einer Zeile im gewählten Viewport angezeigt werden.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => '1',
                            'label' => [
                                'en-GB' => '1',
                                'de-DE' => '1',
                            ]
                        ],
                        [
                            'value' => '2',
                            'label' => [
                                'en-GB' => '2',
                                'de-DE' => '2',
                            ]
                        ],
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_product_listing_columns_sm',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 20,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Smartphones',
                        'de-DE' => 'Smartphones',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Number of columns per row. Indicates how many articles are displayed in one line in the selected viewport.',
                        'de-DE' => 'Anzahl der Spalten pro Zeile. Gibt an, wie viele Artikel in einer Zeile im gewählten Viewport angezeigt werden.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => '1',
                            'label' => [
                                'en-GB' => '1',
                                'de-DE' => '1',
                            ]
                        ],
                        [
                            'value' => '2',
                            'label' => [
                                'en-GB' => '2',
                                'de-DE' => '2',
                            ]
                        ],
                        [
                            'value' => '3',
                            'label' => [
                                'en-GB' => '3',
                                'de-DE' => '3',
                            ]
                        ],
                        [
                            'value' => '4',
                            'label' => [
                                'en-GB' => '4',
                                'de-DE' => '4',
                            ]
                        ],
                        [
                            'value' => '5',
                            'label' => [
                                'en-GB' => '5',
                                'de-DE' => '5',
                            ]
                        ],
                        [
                            'value' => '6',
                            'label' => [
                                'en-GB' => '6',
                                'de-DE' => '6',
                            ]
                        ],
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_product_listing_columns_md',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 30,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Tablets',
                        'de-DE' => 'Tablets',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Number of columns per row. Indicates how many articles are displayed in one line in the selected viewport.',
                        'de-DE' => 'Anzahl der Spalten pro Zeile. Gibt an, wie viele Artikel in einer Zeile im gewählten Viewport angezeigt werden.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => '1',
                            'label' => [
                                'en-GB' => '1',
                                'de-DE' => '1',
                            ]
                        ],
                        [
                            'value' => '2',
                            'label' => [
                                'en-GB' => '2',
                                'de-DE' => '2',
                            ]
                        ],
                        [
                            'value' => '3',
                            'label' => [
                                'en-GB' => '3',
                                'de-DE' => '3',
                            ]
                        ],
                        [
                            'value' => '4',
                            'label' => [
                                'en-GB' => '4',
                                'de-DE' => '4',
                            ]
                        ],
                        [
                            'value' => '5',
                            'label' => [
                                'en-GB' => '5',
                                'de-DE' => '5',
                            ]
                        ],
                        [
                            'value' => '6',
                            'label' => [
                                'en-GB' => '6',
                                'de-DE' => '6',
                            ]
                        ],
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_product_listing_columns_lg',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 40,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Laptops',
                        'de-DE' => 'Laptops',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Number of columns per row. Indicates how many articles are displayed in one line in the selected viewport.',
                        'de-DE' => 'Anzahl der Spalten pro Zeile. Gibt an, wie viele Artikel in einer Zeile im gewählten Viewport angezeigt werden.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => '1',
                            'label' => [
                                'en-GB' => '1',
                                'de-DE' => '1',
                            ]
                        ],
                        [
                            'value' => '2',
                            'label' => [
                                'en-GB' => '2',
                                'de-DE' => '2',
                            ]
                        ],
                        [
                            'value' => '3',
                            'label' => [
                                'en-GB' => '3',
                                'de-DE' => '3',
                            ]
                        ],
                        [
                            'value' => '4',
                            'label' => [
                                'en-GB' => '4',
                                'de-DE' => '4',
                            ]
                        ],
                        [
                            'value' => '5',
                            'label' => [
                                'en-GB' => '5',
                                'de-DE' => '5',
                            ]
                        ],
                        [
                            'value' => '6',
                            'label' => [
                                'en-GB' => '6',
                                'de-DE' => '6',
                            ]
                        ],
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_product_listing_columns_xl',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 50,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Desktop',
                        'de-DE' => 'Desktop',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Number of columns per row. Indicates how many articles are displayed in one line in the selected viewport.',
                        'de-DE' => 'Anzahl der Spalten pro Zeile. Gibt an, wie viele Artikel in einer Zeile im gewählten Viewport angezeigt werden.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => '1',
                            'label' => [
                                'en-GB' => '1',
                                'de-DE' => '1',
                            ]
                        ],
                        [
                            'value' => '2',
                            'label' => [
                                'en-GB' => '2',
                                'de-DE' => '2',
                            ]
                        ],
                        [
                            'value' => '3',
                            'label' => [
                                'en-GB' => '3',
                                'de-DE' => '3',
                            ]
                        ],
                        [
                            'value' => '4',
                            'label' => [
                                'en-GB' => '4',
                                'de-DE' => '4',
                            ]
                        ],
                        [
                            'value' => '5',
                            'label' => [
                                'en-GB' => '5',
                                'de-DE' => '5',
                            ]
                        ],
                        [
                            'value' => '6',
                            'label' => [
                                'en-GB' => '6',
                                'de-DE' => '6',
                            ]
                        ],
                    ]
                ],
            ],
        ];

        $customCategoryHeaderFields = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_header_position',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 10,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Header overlay',
                        'de-DE' => 'Header überlagernd',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'The header can overlay the shopping experience in listings. An impressive layout is created especially when overlaying images, sliders and videos.',
                        'de-DE' => 'Der Header kann auf Kategorieseiten die Erlebniswelt überlagern.  Insbesondere beim Überlagern von Bilder, Slidern und Videos entsteht ein eindrucksvolles Layout.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => 'absolute',
                            'label' => [
                                'en-GB' => 'Yes',
                                'de-DE' => 'Ja',
                            ]
                        ],
                        [
                            'value' => 'relative',
                            'label' => [
                                'en-GB' => 'No',
                                'de-DE' => 'Nein',
                            ]
                        ],
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_header_bg_opacity',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 20,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Header background opacity',
                        'de-DE' => 'Header Hintergrund Deckkraft',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => '0',
                            'label' => [
                                'en-GB' => '0%',
                                'de-DE' => '0%',
                            ]
                        ],
                        [
                            'value' => '5',
                            'label' => [
                                'en-GB' => '5%',
                                'de-DE' => '5%',
                            ]
                        ],
                        [
                            'value' => '10',
                            'label' => [
                                'en-GB' => '10%',
                                'de-DE' => '10%',
                            ]
                        ],
                        [
                            'value' => '15',
                            'label' => [
                                'en-GB' => '15%',
                                'de-DE' => '15%',
                            ]
                        ],
                        [
                            'value' => '20',
                            'label' => [
                                'en-GB' => '20%',
                                'de-DE' => '20%',
                            ]
                        ],
                        [
                            'value' => '25',
                            'label' => [
                                'en-GB' => '25%',
                                'de-DE' => '25%',
                            ]
                        ],
                        [
                            'value' => '30',
                            'label' => [
                                'en-GB' => '30%',
                                'de-DE' => '30%',
                            ]
                        ],
                        [
                            'value' => '35',
                            'label' => [
                                'en-GB' => '35%',
                                'de-DE' => '35%',
                            ]
                        ],
                        [
                            'value' => '40',
                            'label' => [
                                'en-GB' => '40%',
                                'de-DE' => '40%',
                            ]
                        ],
                        [
                            'value' => '45',
                            'label' => [
                                'en-GB' => '45%',
                                'de-DE' => '45%',
                            ]
                        ],
                        [
                            'value' => '50',
                            'label' => [
                                'en-GB' => '50%',
                                'de-DE' => '50%',
                            ]
                        ],
                        [
                            'value' => '55',
                            'label' => [
                                'en-GB' => '55%',
                                'de-DE' => '55%',
                            ]
                        ],
                        [
                            'value' => '60',
                            'label' => [
                                'en-GB' => '60%',
                                'de-DE' => '60%',
                            ]
                        ],
                        [
                            'value' => '65',
                            'label' => [
                                'en-GB' => '65%',
                                'de-DE' => '65%',
                            ]
                        ],
                        [
                            'value' => '70',
                            'label' => [
                                'en-GB' => '70%',
                                'de-DE' => '70%',
                            ]
                        ],
                        [
                            'value' => '75',
                            'label' => [
                                'en-GB' => '75%',
                                'de-DE' => '75%',
                            ]
                        ],
                        [
                            'value' => '80',
                            'label' => [
                                'en-GB' => '80%',
                                'de-DE' => '80%',
                            ]
                        ],
                        [
                            'value' => '85',
                            'label' => [
                                'en-GB' => '85%',
                                'de-DE' => '85%',
                            ]
                        ],
                        [
                            'value' => '90',
                            'label' => [
                                'en-GB' => '90%',
                                'de-DE' => '90%',
                            ]
                        ],
                        [
                            'value' => '95',
                            'label' => [
                                'en-GB' => '95%',
                                'de-DE' => '95%',
                            ]
                        ],
                        [
                            'value' => '100',
                            'label' => [
                                'en-GB' => '100%',
                                'de-DE' => '100%',
                            ]
                        ]
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_nav_main_opacity',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 30,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Main navigation background opacity',
                        'de-DE' => 'Hauptnavigation Hintergrund Deckkraft',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => '0',
                            'label' => [
                                'en-GB' => '0%',
                                'de-DE' => '0%',
                            ]
                        ],
                        [
                            'value' => '5',
                            'label' => [
                                'en-GB' => '5%',
                                'de-DE' => '5%',
                            ]
                        ],
                        [
                            'value' => '10',
                            'label' => [
                                'en-GB' => '10%',
                                'de-DE' => '10%',
                            ]
                        ],
                        [
                            'value' => '15',
                            'label' => [
                                'en-GB' => '15%',
                                'de-DE' => '15%',
                            ]
                        ],
                        [
                            'value' => '20',
                            'label' => [
                                'en-GB' => '20%',
                                'de-DE' => '20%',
                            ]
                        ],
                        [
                            'value' => '25',
                            'label' => [
                                'en-GB' => '25%',
                                'de-DE' => '25%',
                            ]
                        ],
                        [
                            'value' => '30',
                            'label' => [
                                'en-GB' => '30%',
                                'de-DE' => '30%',
                            ]
                        ],
                        [
                            'value' => '35',
                            'label' => [
                                'en-GB' => '35%',
                                'de-DE' => '35%',
                            ]
                        ],
                        [
                            'value' => '40',
                            'label' => [
                                'en-GB' => '40%',
                                'de-DE' => '40%',
                            ]
                        ],
                        [
                            'value' => '45',
                            'label' => [
                                'en-GB' => '45%',
                                'de-DE' => '45%',
                            ]
                        ],
                        [
                            'value' => '50',
                            'label' => [
                                'en-GB' => '50%',
                                'de-DE' => '50%',
                            ]
                        ],
                        [
                            'value' => '55',
                            'label' => [
                                'en-GB' => '55%',
                                'de-DE' => '55%',
                            ]
                        ],
                        [
                            'value' => '60',
                            'label' => [
                                'en-GB' => '60%',
                                'de-DE' => '60%',
                            ]
                        ],
                        [
                            'value' => '65',
                            'label' => [
                                'en-GB' => '65%',
                                'de-DE' => '65%',
                            ]
                        ],
                        [
                            'value' => '70',
                            'label' => [
                                'en-GB' => '70%',
                                'de-DE' => '70%',
                            ]
                        ],
                        [
                            'value' => '75',
                            'label' => [
                                'en-GB' => '75%',
                                'de-DE' => '75%',
                            ]
                        ],
                        [
                            'value' => '80',
                            'label' => [
                                'en-GB' => '80%',
                                'de-DE' => '80%',
                            ]
                        ],
                        [
                            'value' => '85',
                            'label' => [
                                'en-GB' => '85%',
                                'de-DE' => '85%',
                            ]
                        ],
                        [
                            'value' => '90',
                            'label' => [
                                'en-GB' => '90%',
                                'de-DE' => '90%',
                            ]
                        ],
                        [
                            'value' => '95',
                            'label' => [
                                'en-GB' => '95%',
                                'de-DE' => '95%',
                            ]
                        ],
                        [
                            'value' => '100',
                            'label' => [
                                'en-GB' => '100%',
                                'de-DE' => '100%',
                            ]
                        ]
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_header_contrast',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 40,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Header contrast',
                        'de-DE' => 'Header Kontrast',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Specifies the contrast colors of the header. When overlaying dark elements, use <strong>Dark Header - light text</strong> for optimal contrast. <br>In the settings for logos you can also set the respective logo for the different headers.',
                        'de-DE' => 'Legt die Kontrastfarben des Headers fest. Bei Überlagerung dunkler Elemente verwende <strong>Dunkler Header - heller Text</strong> für einen optimalen Kontrast. <br>In den Einstellungen für Logos kannst Du ebenfalls die jeweiligen Logo für die verschiedenen Header einstellen.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => 'default-header',
                            'label' => [
                                'en-GB' => 'Default header',
                                'de-DE' => 'Standard Header',
                            ]
                        ],
                        [
                            'value' => 'light-header',
                            'label' => [
                                'en-GB' => 'Light header - dark text',
                                'de-DE' => 'Heller Header - dunkler Text',
                            ]
                        ],
                        [
                            'value' => 'dark-header',
                            'label' => [
                                'en-GB' => 'Dark header - light text',
                                'de-DE' => 'Dunkler Header - heller Text',
                            ]
                        ],
                        [
                            'value' => 'inverted-header',
                            'label' => [
                                'en-GB' => 'Inverted header',
                                'de-DE' => 'Invertierter Header',
                            ]
                        ],
                    ]
                ],
            ]
        ];

        $customCategoryImageFields = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_image',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 10,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Use first section of listing layout as "Hero section"',
                        'de-DE' => 'Erste Sektion des Kategorie Layouts als "Hero Sektion" verwenden',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'If active, the first section of the listing page layout is used as the "hero section". This includes displaying a category image and/or background color in the first section. The configurations for background image, background color and image mode set in the first section serve as a fallback. You can make different settings in the category in the "Hero section" tab. Find out more about this feature in the documentary.',
                        'de-DE' => 'Wenn aktiv, wird die erste Sektion des Kategorieseite-Layouts als "Hero Sektion" verwendet. Das beinhaltet die Darstellung eines Kategoriebildes und/oder Hintergrundfarbe im Bereich der ersten Sektion. Als Fallback dienen die in der ersten Sektion eingestellten Konfigurationen für Hintergrundbild, Hintergrundfarbe und Bildmodus. In der Kategorie im Reiter "Hero Sektion" kannst Du abweichende Einstellungen tätigen. Erfahre in der Doku mehr über diese Funktion.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => 'display',
                            'label' => [
                                'en-GB' => 'Yes',
                                'de-DE' => 'Ja',
                            ]
                        ],
                        [
                            'value' => 'hide',
                            'label' => [
                                'en-GB' => 'No',
                                'de-DE' => 'Nein',
                            ]
                        ],
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_image_contrast',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 20,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Text contrast',
                        'de-DE' => 'Text Kontrast',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Specifies the contrast color of the texts in the foreground. For dark backgrounds, use light text for optimal contrast.',
                        'de-DE' => 'Legt die Kontrastfarbe der Texte im Vordergrund fest. Bei dunklen Hintergründen verwende heller Text für einen optimalen Kontrast.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => 'light-text',
                            'label' => [
                                'en-GB' => 'Light text',
                                'de-DE' => 'Heller Text',
                            ]
                        ],
                        [
                            'value' => 'dark-text',
                            'label' => [
                                'en-GB' => 'Dark Text',
                                'de-DE' => 'Dunkler Text',
                            ]
                        ]
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_image_color',
                'type' => CustomFieldTypes::COLORPICKER,
                'config' => [
                    'customFieldPosition' => 30,
                    'label' => [
                        'en-GB' => 'Background color',
                        'de-DE' => 'Hintergrundfarbe',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Overwrites the background color set in the shopping experience for the first section.',
                        'de-DE' => 'Überschreibt die in der Erlebniswelt gesetzte Hintergrundfarbe der ersten Sektion.',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_image_opacity',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 40,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Opacity',
                        'de-DE' => 'Sichtbarkeit',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Opacity of the category image above tha background color. With this option, the background image can be subdued and the text above it can be made more legible.',
                        'de-DE' => 'Sichtbarkeit des Hintergrundbildes über der Hintergrundfarbe. Mit dieser Option kann das Hintergrundbild gedämpft werden und darüberliegender Text lesbarer gestaltet werden.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => '0',
                            'label' => [
                                'en-GB' => '0%',
                                'de-DE' => '0%',
                            ]
                        ],
                        [
                            'value' => '5',
                            'label' => [
                                'en-GB' => '5%',
                                'de-DE' => '5%',
                            ]
                        ],
                        [
                            'value' => '10',
                            'label' => [
                                'en-GB' => '10%',
                                'de-DE' => '10%',
                            ]
                        ],
                        [
                            'value' => '15',
                            'label' => [
                                'en-GB' => '15%',
                                'de-DE' => '15%',
                            ]
                        ],
                        [
                            'value' => '20',
                            'label' => [
                                'en-GB' => '20%',
                                'de-DE' => '20%',
                            ]
                        ],
                        [
                            'value' => '25',
                            'label' => [
                                'en-GB' => '25%',
                                'de-DE' => '25%',
                            ]
                        ],
                        [
                            'value' => '30',
                            'label' => [
                                'en-GB' => '30%',
                                'de-DE' => '30%',
                            ]
                        ],
                        [
                            'value' => '35',
                            'label' => [
                                'en-GB' => '35%',
                                'de-DE' => '35%',
                            ]
                        ],
                        [
                            'value' => '40',
                            'label' => [
                                'en-GB' => '40%',
                                'de-DE' => '40%',
                            ]
                        ],
                        [
                            'value' => '45',
                            'label' => [
                                'en-GB' => '45%',
                                'de-DE' => '45%',
                            ]
                        ],
                        [
                            'value' => '50',
                            'label' => [
                                'en-GB' => '50%',
                                'de-DE' => '50%',
                            ]
                        ],
                        [
                            'value' => '55',
                            'label' => [
                                'en-GB' => '55%',
                                'de-DE' => '55%',
                            ]
                        ],
                        [
                            'value' => '60',
                            'label' => [
                                'en-GB' => '60%',
                                'de-DE' => '60%',
                            ]
                        ],
                        [
                            'value' => '65',
                            'label' => [
                                'en-GB' => '65%',
                                'de-DE' => '65%',
                            ]
                        ],
                        [
                            'value' => '70',
                            'label' => [
                                'en-GB' => '70%',
                                'de-DE' => '70%',
                            ]
                        ],
                        [
                            'value' => '75',
                            'label' => [
                                'en-GB' => '75%',
                                'de-DE' => '75%',
                            ]
                        ],
                        [
                            'value' => '80',
                            'label' => [
                                'en-GB' => '80%',
                                'de-DE' => '80%',
                            ]
                        ],
                        [
                            'value' => '85',
                            'label' => [
                                'en-GB' => '85%',
                                'de-DE' => '85%',
                            ]
                        ],
                        [
                            'value' => '90',
                            'label' => [
                                'en-GB' => '90%',
                                'de-DE' => '90%',
                            ]
                        ],
                        [
                            'value' => '95',
                            'label' => [
                                'en-GB' => '95%',
                                'de-DE' => '95%',
                            ]
                        ],
                        [
                            'value' => '100',
                            'label' => [
                                'en-GB' => '100%',
                                'de-DE' => '100%',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_image_attachment',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 50,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Attachment',
                        'de-DE' => 'Fixieren',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Determines whether a background image moves with the scroll or keeps its fixed location on the page.',
                        'de-DE' => 'Bestimmt, ob ein Hintergrundbild beim Scrollen mitwandert oder seinen festen Ort auf der Seite behält.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => 'scroll',
                            'label' => [
                                'en-GB' => 'Scroll',
                                'de-DE' => 'Mitscrollend',
                            ]
                        ],
                        [
                            'value' => 'fixed',
                            'label' => [
                                'en-GB' => 'Fixed',
                                'de-DE' => 'Fixiert',
                            ]
                        ]
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_image_position',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 60,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Position',
                        'de-DE' => 'Position',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Sets the position of the background image.',
                        'de-DE' => 'Legt Position eines Hintergrundbildes fest.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => 'left top',
                            'label' => [
                                'en-GB' => 'left top',
                                'de-DE' => 'left top',
                            ]
                        ],
                        [
                            'value' => 'left center',
                            'label' => [
                                'en-GB' => 'left center',
                                'de-DE' => 'left center',
                            ]
                        ],
                        [
                            'value' => 'left bottom',
                            'label' => [
                                'en-GB' => 'left bottom',
                                'de-DE' => 'left bottom',
                            ]
                        ],
                        [
                            'value' => 'center top',
                            'label' => [
                                'en-GB' => 'center top',
                                'de-DE' => 'center top',
                            ]
                        ],
                        [
                            'value' => 'center center',
                            'label' => [
                                'en-GB' => 'center center',
                                'de-DE' => 'center center',
                            ]
                        ],
                        [
                            'value' => 'center bottom',
                            'label' => [
                                'en-GB' => 'center bottom',
                                'de-DE' => 'center bottom',
                            ]
                        ],
                        [
                            'value' => 'right top',
                            'label' => [
                                'en-GB' => 'right top',
                                'de-DE' => 'right top',
                            ]
                        ],
                        [
                            'value' => 'right center',
                            'label' => [
                                'en-GB' => 'right center',
                                'de-DE' => 'right center',
                            ]
                        ],
                        [
                            'value' => 'right bottom',
                            'label' => [
                                'en-GB' => 'right bottom',
                                'de-DE' => 'right bottom',
                            ]
                        ],
                    ]
                ],
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_image_mode',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 70,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Image mode',
                        'de-DE' => 'Bildmodus',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from section settings',
                        'de-DE' => 'Vererbt aus Sektionseinstellungen',
                    ],
                    'helpText' => [
                        'en-GB' => 'Determines the display mode of the background image. The \'Inherit\' option adopts the setting from the section setting of the shopping experience layout in which the background image is displayed.',
                        'de-DE' => 'Bestimmt den Anzeigemodus des Hintergrundbildes. Die Option \'Vererbt\' übernimmt die Einstellung aus der Sektion-Einstellung des Erlebniswelt-Layouts in welcher das Hintergrundbild angezeigt wird.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from section settings',
                                'de-DE' => 'Vererbt aus Sektionseinstellungen',
                            ]
                        ],
                        [
                            'value' => 'auto',
                            'label' => [
                                'en-GB' => 'Auto',
                                'de-DE' => 'Auto',
                            ]
                        ],
                        [
                            'value' => 'cover',
                            'label' => [
                                'en-GB' => 'Cover',
                                'de-DE' => 'Füllen',
                            ]
                        ],
                        [
                            'value' => 'contain',
                            'label' => [
                                'en-GB' => 'Contain',
                                'de-DE' => 'Beinhalten'
                            ]
                        ]
                    ]
                ],
            ],
        ];

        $customProductFields = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_features',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'customFieldType' => 'text',
                    'customFieldPosition' => 0,
                    'label' => [
                        'en-GB' => 'Product features',
                        'de-DE' => 'Produkt Features',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Feature 1; Feature 2; Feature 3; ...;',
                        'de-DE' => 'Feature 1; Feature 2; Feature 3; ...;',
                    ],
                    'helpText' => [
                        'en-GB' => 'Multiple features can be entered separated by semicolon.',
                        'de-DE' => 'Mehrere Features können, durch ein Semikolon getrennt, eingetragen werden.',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_youtube_ids',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'customFieldType' => 'text',
                    'customFieldPosition' => 10,
                    'label' => [
                        'en-GB' => 'Youtube IDs / Urls',
                        'de-DE' => 'Youtube IDs / Urls',
                    ],
                    'placeholder' => [
                        'en-GB' => 'KCRgZXXmJG4,oQAFy229bEU',
                        'de-DE' => 'KCRgZXXmJG4,oQAFy229bEU',
                    ],
                    'helpText' => [
                        'en-GB' => 'Multiple IDs can be entered separated by commas. You will find the YouTube ID in the 11-digit number at the end of the YouTube link in the address line (?v= XXXXXXXXXX))',
                        'de-DE' => 'Mehrere IDs können kommasepariert eingetragen werden. Du findest die Youtube ID in der 11-stelligen Nummer am Ende des Youtube-Links in der Adresszeile (?v=  XXXXXXXXXX))',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_vimeo_ids',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'customFieldType' => 'text',
                    'customFieldPosition' => 10,
                    'label' => [
                        'en-GB' => 'Vimeo IDs / Urls',
                        'de-DE' => 'Vimeo IDs / Urls',
                    ],
                    'placeholder' => [
                        'en-GB' => '68370399,69942635',
                        'de-DE' => '68370399,69942635',
                    ],
                    'helpText' => [
                        'en-GB' => 'Multiple IDs can be entered separated by commas.',
                        'de-DE' => 'Mehrere IDs können kommasepariert eingetragen werden.',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_custom_tab1_title',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'customFieldType' => 'text',
                    'customFieldPosition' => 20,
                    'label' => [
                        'en-GB' => 'Tab 1 title',
                        'de-DE' => 'Tab 1 Titel',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_custom_tab1_text',
                'type' => CustomFieldTypes::HTML,
                'config' => [
                    'componentName' => 'sw-text-editor',
                    'customFieldType' => 'textEditor',
                    'customFieldPosition' => 25,
                    'label' => [
                        'en-GB' => 'Tab 1 text',
                        'de-DE' => 'Tab 1 Text',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_custom_tab2_title',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'customFieldType' => 'text',
                    'customFieldPosition' => 30,
                    'label' => [
                        'en-GB' => 'Tab 2 title',
                        'de-DE' => 'Tab 2 Titel',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_custom_tab2_text',
                'type' => CustomFieldTypes::HTML,
                'config' => [
                    'componentName' => 'sw-text-editor',
                    'customFieldType' => 'textEditor',
                    'customFieldPosition' => 35,
                    'label' => [
                        'en-GB' => 'Tab 2 text',
                        'de-DE' => 'Tab 2 Text',
                    ]
                ]
            ]
        ];

        $customProductDatasheets = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_datasheet_one',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'componentName' => 'sw-media-field',
                    'customFieldType' => "media",
                    'customFieldPosition' => 10,
                    'label' => [
                        'en-GB' => 'Datasheet 1',
                        'de-DE' => 'Datenblatt 1',
                    ],
                    'helpText' => [
                        'en-GB' => 'Displays a file in the datasheets tab. Useful file formats PDF, TXT, JPG, PNG or GIF.',
                        'de-DE' => 'Stellt eine Datei im Tab für Datenblätter dar. Sinnvolle Dateiformate PDF, TXT, JPG, PNG oder GIF.',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_datasheet_two',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'componentName' => 'sw-media-field',
                    'customFieldType' => "media",
                    'customFieldPosition' => 15,
                    'label' => [
                        'en-GB' => 'Datasheet 2',
                        'de-DE' => 'Datenblatt 2',
                    ],
                    'helpText' => [
                        'en-GB' => 'Displays a file in the datasheets tab. Useful file formats PDF, TXT, JPG, PNG or GIF.',
                        'de-DE' => 'Stellt eine Datei im Tab für Datenblätter dar. Sinnvolle Dateiformate PDF, TXT, JPG, PNG oder GIF.',
                    ]
                ]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_datasheet_three',
                'type' => CustomFieldTypes::TEXT,
                'config' => [
                    'componentName' => 'sw-media-field',
                    'customFieldType' => "media",
                    'customFieldPosition' => 20,
                    'label' => [
                        'en-GB' => 'Datasheet 3',
                        'de-DE' => 'Datenblatt 3',
                    ],
                    'helpText' => [
                        'en-GB' => 'Displays a file in the datasheets tab. Useful file formats PDF, TXT, JPG, PNG or GIF.',
                        'de-DE' => 'Stellt eine Datei im Tab für Datenblätter dar. Sinnvolle Dateiformate PDF, TXT, JPG, PNG oder GIF.',
                    ]
                ]
            ],
        ];

        $customProductGallery = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_gallery_mode',
                'type' => CustomFieldTypes::SELECT,
                'config' => [
                    'customFieldPosition' => 10,
                    'componentName' => 'sw-single-select',
                    'customFieldType' => 'select',
                    'label' => [
                        'en-GB' => 'Gallery mode',
                        'de-DE' => 'Galerie Modus',
                    ],
                    'placeholder' => [
                        'en-GB' => 'Inherit from theme configuration',
                        'de-DE' => 'Vererbt aus Theme-Konfiguration'
                    ],
                    'helpText' => [
                        'en-GB' => 'Switches the image gallery either to slider mode (standard) or to scroller mode.',
                        'de-DE' => 'Schaltet die Image Galerie entweder in den Slider-Modus (Standard) oder in den Scroller Modus.',
                    ],
                    'options' => [
                        [
                            'value' => 'inherit',
                            'label' => [
                                'en-GB' => 'Inherit from theme configuration',
                                'de-DE' => 'Vererbt aus Theme-Konfiguration',
                            ]
                        ],
                        [
                            'value' => 'slider',
                            'label' => [
                                'en-GB' => 'Gallery Slider',
                                'de-DE' => 'Galerie-Slider',
                            ]
                        ],
                        [
                            'value' => 'scroller',
                            'label' => [
                                'en-GB' => 'Gallery Scroller',
                                'de-DE' => 'Galerie Scroller',
                            ]
                        ],
                        [
                            'value' => 'grid',
                            'label' => [
                                'en-GB' => 'Gallery Grid',
                                'de-DE' => 'Galerie Raster',
                            ]
                        ]
                    ]
                ],
            ]
        ];

        $customCmsFieldSets = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_cms_block',
                'config' => [
                    'label' => [
                        'en-GB' => 'CMS Block settings (' . ucfirst(self::THEME_INSTANCE) . ')',
                        'de-DE' => 'CMS Block Einstellungen (' . ucfirst(self::THEME_INSTANCE) . ')',
                    ]
                ],
                'customFields' => $customCmsFields,
                'relations' => [$relationCms]
            ],
        ];

        $customFieldSets = [
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_header_set',
                'config' => [
                    'label' => [
                        'en-GB' => 'Header (' . ucfirst(self::THEME_INSTANCE) . ')',
                        'de-DE' => 'Header (' . ucfirst(self::THEME_INSTANCE) . ')',
                    ]
                ],
                'customFields' => $customCategoryHeaderFields,
                'relations' => [$relationCategory1, $relationLanding1]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_category_image_set',
                'config' => [
                    'label' => [
                        'en-GB' => 'Hero section (' . ucfirst(self::THEME_INSTANCE) . ')',
                        'de-DE' => 'Hero Sektion (' . ucfirst(self::THEME_INSTANCE) . ')',
                    ]
                ],
                'customFields' => $customCategoryImageFields,
                'relations' => [$relationCategory2]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_additional_set',
                'config' => [
                    'label' => [
                        'en-GB' => 'Additional (' . ucfirst(self::THEME_INSTANCE) . ')',
                        'de-DE' => 'Weitere (' . ucfirst(self::THEME_INSTANCE) . ')',
                    ]
                ],
                'customFields' => $customCategoryFields,
                'relations' => [$relationCategory3]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_product_listing_columns_set',
                'config' => [
                    'label' => [
                        'en-GB' => 'Products per row (' . ucfirst(self::THEME_INSTANCE) . ')',
                        'de-DE' => 'Produkte pro Zeile (' . ucfirst(self::THEME_INSTANCE) . ')',
                    ]
                ],
                'customFields' => $productListingColumnsCategoryFields,
                'relations' => [$relationCategory4]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_tabs_set',
                'config' => [
                    'label' => [
                        'en-GB' => 'Custom content (' . ucfirst(self::THEME_INSTANCE) . ')',
                        'de-DE' => 'Zusatzinhalte (' . ucfirst(self::THEME_INSTANCE) . ')',
                    ]
                ],
                'customFields' => $customProductFields,
                'relations' => [$relationProduct1]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_datasheets_set',
                'config' => [
                    'label' => [
                        'en-GB' => 'Datasheets (' . ucfirst(self::THEME_INSTANCE) . ')',
                        'de-DE' => 'Datenblätter (' . ucfirst(self::THEME_INSTANCE) . ')',
                    ]
                ],
                'customFields' => $customProductDatasheets,
                'relations' => [$relationProduct2]
            ],
            [
                'id' => Uuid::randomHex(),
                'name' => 'zenit_' . self::THEME_INSTANCE . '_gallery_set',
                'config' => [
                    'label' => [
                        'en-GB' => 'Gallery (' . ucfirst(self::THEME_INSTANCE) . ')',
                        'de-DE' => 'Galerie (' . ucfirst(self::THEME_INSTANCE) . ')',
                    ]
                ],
                'customFields' => $customProductGallery,
                'relations' => [$relationProduct3]
            ],
        ];

        //get CMS fieldnames
        $cmsFields = [];
        foreach ($customCmsFields as $field) {
            $cmsFields[] = $field['name'];
        }

        $this->createCustomFields($customFieldSets, $context);
        $this->createCustomFields($customCmsFieldSets, $context, $cmsFields);

    }

    /**
     * Method to create the customfields.
     *
     * @param array $fields
     * @param Context $context
     * @param array|null $cmsFields
     *
     */
    private function createCustomFields(Array $customFieldSets, Context $context, Array $cmsFields = null): void
    {
        //don't create cms field, if it already exists
        if($cmsFields) {
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsAnyFilter('name', $cmsFields));

            if ($this->customFieldSetRepository->search($criteria, $context)->getTotal() !== 0) {
                return;
            }
        }

        $customFieldSetIndex = 0;
        $customFieldIndex = 0;

        foreach ($customFieldSets as $customFieldSet) {
            //handle relations and customFieldSetIds
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('name', $customFieldSet['name']));
            $criteria->addAssociation('relations');

            $result = $this->customFieldSetRepository->search($criteria, $context);

            if ($result->getTotal() !== 0) {
                $customFieldSets[$customFieldSetIndex]['id'] = array_keys($result->getElements())[0];

                if ($customFieldSet['relations']) {
                    foreach ($customFieldSet['relations'] as $key => $relation) {
                        $currentRelations = $result->getElements()[array_keys($result->getElements())[0]]->get('relations')->getElements();

                        if (!empty($currentRelations)) {
                            if (isset(array_keys($currentRelations)[$key])) {
                                if (isset($currentRelations[array_keys($currentRelations)[$key]])){
                                    $customFieldSets[$customFieldSetIndex]['relations'][$key]['id'] = $currentRelations[array_keys($currentRelations)[$key]]->get('id');
                                }
                            }
                        }
                    }
                }
            }

            //handle customFieldIds
            foreach ($customFieldSet['customFields'] as $customField) {
                $criteria = new Criteria();
                $criteria->addFilter(new EqualsFilter('name', $customField['name']));

                $result = $this->customFieldRepository->search($criteria, $context);

                if ($result->getTotal() !== 0) {
                    $customFieldSets[$customFieldSetIndex]['customFields'][$customFieldIndex]['id'] = array_keys($result->getElements())[0];
                }

                $customFieldIndex++;
            }

            $customFieldSetIndex++;
            $customFieldIndex = 0;
        }

        //catch if cloud theme is installed
        try {
            $this->customFieldSetRepository->upsert($customFieldSets, $context);
        } catch (Exception $e) {}
    }


    /**
     * Method to delete the customfields.
     *
     * @param Context $context
     *
     */
    public function deleteCustomFields($context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new ContainsFilter('name', 'zenit_' . self::THEME_INSTANCE));

        $criteriaResult = $this->customFieldSetRepository->search($criteria, $context);
        $ids = $criteriaResult->getIds();

        if(!empty($ids)) {
            $data = [];

            foreach ($ids as $id) {
                $data[] = [
                    'id' => $id
                ];
            }

            $this->customFieldSetRepository->delete($data, $context);
        }
    }
}