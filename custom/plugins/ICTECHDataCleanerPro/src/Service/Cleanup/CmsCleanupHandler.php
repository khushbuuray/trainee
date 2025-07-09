<?php

declare(strict_types=1);

namespace ICTECHDataCleanerPro\Service\Cleanup;

use Shopware\Core\Content\Cms\CmsPageEntity;

use Shopware\Core\Content\Cms\CmsPageCollection;

use Shopware\Core\Framework\Context;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;

use ICTECHDataCleanerPro\Service\CleanupLoggerService;

class CmsCleanupHandler implements CleanupHandlerInterface

{

    /** @var EntityRepository<CmsPageCollection> */

    private EntityRepository $cmsPageRepository;

    private CleanupLoggerService $logger;

    /**

     * @param EntityRepository<CmsPageCollection> $cmsPageRepository

     */

    public function __construct(EntityRepository $cmsPageRepository, CleanupLoggerService $logger)

    {

        $this->cmsPageRepository = $cmsPageRepository;

        $this->logger = $logger;
    }

    /**

     * @param array<string, mixed> $config

     * @return array{name: string, items: array<string, array{count: int, sample: list<array{id: string, name: string, created_at: string|null}>}>}

     */

    public function cleanup(array $config, bool $dryRun, Context $context): array

    {

        $results = [

            'name' => $this->getName(),

            'items' => [],

        ];

        if (isset($config['cmsPageCleanup.unpublishedDraftsMonths']) && is_numeric($config['cmsPageCleanup.unpublishedDraftsMonths'])) {

            $months = (int) $config['cmsPageCleanup.unpublishedDraftsMonths'];

            $draftResults = $this->cleanupUnpublishedDrafts($months, $dryRun, $context);

            $results['items']['unpublished_drafts'] = $draftResults;
        }

        return $results;
    }

    /**

     * @return array{count: int, sample: list<array{id: string, name: string, created_at: string|null}>}

     */

    private function cleanupUnpublishedDrafts(int $months, bool $dryRun, Context $context): array

    {

        $cutoffDate = (new \DateTime())->modify("-{$months} months");

        $criteria = new Criteria();

        $criteria->addFilter(

            new MultiFilter(MultiFilter::CONNECTION_AND, [

                new EqualsFilter('locked', false),

                new RangeFilter('createdAt', [RangeFilter::LT => $cutoffDate->format(DATE_ATOM)]),

                new NotFilter(NotFilter::CONNECTION_OR, [

                    new EqualsFilter('type', 'product_list'),

                ]),

            ])

        );

        $criteria->addAssociations([

            'categories',

            'landingPages',

            'products',

            'translations',

            'sections.blocks.slots',

        ]);

        /** @var EntitySearchResult<CmsPageCollection> $cmsPages */

        $cmsPages = $this->cmsPageRepository->search($criteria, $context);

        $filtered = $cmsPages->filter(function (CmsPageEntity $page): bool {

            if (

                ($page->getCategories()?->count() ?? 0) > 0 ||

                ($page->getLandingPages()?->count() ?? 0) > 0 ||

                ($page->getProducts()?->count() ?? 0) > 0

            ) {

                return false;
            }

            foreach ($page->getSections() ?? [] as $section) {

                foreach ($section->getBlocks() ?? [] as $block) {

                    if ($block->getLocked()) {

                        return false;
                    }

                    foreach ($block->getSlots() ?? [] as $slot) {

                        if ($slot->getLocked()) {

                            return false;
                        }
                    }
                }
            }

            return true;
        });

        /** @var list<array{id: string, name: string, created_at: string|null}> $sample */

        $sample = [];

        foreach ($filtered as $page) {

            $translatedName = $page->getTranslated()['name'] ?? 'N/A';

            $sample[] = [

                'id' => $page->getId(),

                'name' => is_string($translatedName) ? $translatedName : 'N/A',

                'created_at' => $page->getCreatedAt()?->format('Y-m-d H:i:s'),

            ];
        }

        $this->logger->logToFile('cms', 'info', [

            'function' => 'cleanupUnpublishedDrafts',

            'cutoff' => $cutoffDate->format(DATE_ATOM),

            'count' => $filtered->count(),

        ]);

        if (!$dryRun && $filtered->count() > 0) {

            $ids = array_values(array_map(
                static fn(CmsPageEntity $entity): array => ['id' => $entity->getId()],
                iterator_to_array($filtered)
            ));

            try {

                $this->cmsPageRepository->delete($ids, $context);

                $this->logger->logSuccess('cms', [

                    'action' => 'delete_unpublished_drafts',

                    'count' => count($ids),

                    'ids' => array_column($ids, 'id'),

                ]);
            } catch (\Throwable $e) {

                $this->logger->logError('cms', $e, [

                    'action' => 'delete_unpublished_drafts',

                    'ids' => array_column($ids, 'id'),

                ]);
            }
        }

        return [

            'count' => $filtered->count(),

            'sample' => $sample,

        ];
    }

    public function getName(): string

    {

        return 'CMS Content & Page Cleanup';
    }

    public function getKey(): string
    {

        return 'cmsCleanup';
    }
}
