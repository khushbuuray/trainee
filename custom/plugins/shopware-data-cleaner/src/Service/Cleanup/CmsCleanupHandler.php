<?php

declare(strict_types=1);

namespace IctDataCleanerPro\Service\Cleanup;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;

class CmsCleanupHandler implements CleanupHandlerInterface
{
    private EntityRepository $cmsPageRepository;

    public function __construct(EntityRepository $cmsPageRepository)
    {
        $this->cmsPageRepository = $cmsPageRepository;
    }

    public function cleanup(array $config, bool $dryRun, Context $context): array
    {
        $results = [
            'name' => $this->getName(),
            'items' => []
        ];

        // Clean unpublished CMS drafts
        if (isset($config['cmsPageCleanup.unpublishedDraftsMonths'])) {
            $draftResults = $this->cleanupUnpublishedDrafts(
                (int) $config['cmsPageCleanup.unpublishedDraftsMonths'],
                $dryRun,
                $context
            );
            $results['items']['unpublished_drafts'] = $draftResults;
        }

        return $results;
    }

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
                ])
            ])
        );

        $criteria->addAssociations([
            'categories',
            'landingPages',
            'products',
            'translations',
            'sections.blocks.slots',
        ]);

        $cmsPages = $this->cmsPageRepository->search($criteria, $context);

        // Filter pages that are not used anywhere and have no locked blocks/slots
        $filtered = $cmsPages->filter(function ($page) {
            if (
                $page->getCategories()->count() > 0 ||
                $page->getLandingPages()->count() > 0 ||
                $page->getProducts()->count() > 0
            ) {
                return false;
            }

            foreach ($page->getSections() as $section) {
                foreach ($section->getBlocks() as $block) {
                    if ($block->getLocked()) {
                        return false;
                    }
                    foreach ($block->getSlots() as $slot) {
                        if ($slot->getLocked()) {
                            return false;
                        }
                    }
                }
            }

            return true;
        });

        // Prepare a clean preview sample
        $sample = [];
        foreach ($filtered as $page) {
            $sample[] = [
                'id' => $page->getId(),
                'name' => $page->getTranslated()['name'] ?? 'N/A',
                'created_at' => $page->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        // Delete if not a dry run
        if (!$dryRun && $filtered->count() > 0) {
            $ids = [];
            foreach ($filtered->getEntities() as $entity) {
                $ids[] = ['id' => $entity->getId()];
            }
            $this->cmsPageRepository->delete($ids, $context);
        }

        return [
            'count' => $filtered->count(),
            'sample' => array_slice($sample, 0, 5),
        ];
    }

    public function getName(): string
    {
        return 'CMS Content & Page Cleanup';
    }
}
