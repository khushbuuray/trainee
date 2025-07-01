<?php declare(strict_types=1);

namespace IctDataCleanerPro\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use IctDataCleanerPro\Service\CleanupService;
use IctDataCleanerPro\Service\Cleanup\ProductCleanupHandler;
use IctDataCleanerPro\Core\Content\CleanupLog\CleanupLogEntity;
use IctDataCleanerPro\Core\Content\CleanupLog\CleanupLogCollection;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

/**
 * @RouteScope(scopes={"api"})
 */
class CleanupController extends AbstractController
{
    private CleanupService $cleanupService;

    /** @var EntityRepository<CleanupLogCollection> */
    private EntityRepository $cleanupLogRepository;

    private ProductCleanupHandler $productCleanupHandler;

    /**
     * @param EntityRepository<CleanupLogCollection> $cleanupLogRepository
     */
    public function __construct(
        CleanupService $cleanupService,
        EntityRepository $cleanupLogRepository,
        ProductCleanupHandler $productCleanupHandler
    ) {
        $this->cleanupService = $cleanupService;
        $this->cleanupLogRepository = $cleanupLogRepository;
        $this->productCleanupHandler = $productCleanupHandler;
    }

    /**
     * @Route("/api/ict-data-cleaner/preview", name="api.ict_data_cleaner.preview", methods={"POST"})
     */
    public function preview(Request $request, Context $context): JsonResponse
    {
        try {
            $results = $this->cleanupService->previewCleanup($context);

            return new JsonResponse([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @Route("/api/ict-data-cleaner/preview-products-not-sold", name="api.ict_data_cleaner.products_not_sold", methods={"GET", "POST"})
     */
    public function previewProductsNotSoldIn(Request $request, Context $context): JsonResponse
    {
        try {
            $monthsRaw = $request->get('months');
            $months = is_numeric($monthsRaw) ? (int) $monthsRaw : 6;

            $config = ['productCleanup.monthsNotSold' => $months];
            $results = $this->productCleanupHandler->cleanup($config, false, $context);

            return new JsonResponse([
                'success' => true,
                'data' => $results['items']['products_not_sold'] ?? []
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @Route("/api/ict-data-cleaner/cleanup", name="api.ict_data_cleaner.cleanup", methods={"POST"})
     */
    public function cleanup(Request $request, string $module = null): JsonResponse
    {
        try {
            $context = Context::createDefaultContext();
            $dryRun = $request->request->getBoolean('dryRun', false);
            $results = $this->cleanupService->runCleanup($context, $dryRun, 'scheduled', $module);

            return new JsonResponse([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @Route("/api/ict-data-cleaner/logs", name="api.ict_data_cleaner.logs", methods={"GET"})
     */
    public function logs(Request $request, Context $context): JsonResponse
    {
        try {
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 25);

            $criteria = new Criteria();
            $criteria->setLimit($limit);
            $criteria->setOffset(($page - 1) * $limit);
            $criteria->addSorting(new FieldSorting('runAt', FieldSorting::DESCENDING));

            /** @var EntitySearchResult<CleanupLogCollection> $logs */
            $logs = $this->cleanupLogRepository->search($criteria, $context);

            $logData = [];

            foreach ($logs->getEntities() as $log) {
                $logData[] = [
                    'id' => $log->getId(),
                    'runAt' => $log->getRunAt()->format('Y-m-d H:i:s'),
                    'trigger' => $log->getTrigger(),
                    'mode' => $log->getMode(),
                    'results' => $log->getResults(),
                ];
            }


            return new JsonResponse([
                'success' => true,
                'data' => [
                    'items' => $logData,
                    'total' => $logs->getTotal()
                ]
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}