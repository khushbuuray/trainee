<?php declare(strict_types=1);

namespace IctDataCleanerPro\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use IctDataCleanerPro\Service\CleanupService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Context;

class CleanupController extends AbstractController
{
    private CleanupService $cleanupService;
    private EntityRepository $cleanupLogRepository;

    public function __construct(
        CleanupService $cleanupService,
        EntityRepository $cleanupLogRepository
    ) {
        $this->cleanupService = $cleanupService;
        $this->cleanupLogRepository = $cleanupLogRepository;
    }
    /**
     * @Route("/api/_action/ict-data-cleaner/preview", name="api.ict_data_cleaner.preview", methods={"POST"})
     */
    public function preview(Request $request, Context $context): JsonResponse
    {
        try {
            $results = $this->cleanupService->previewCleanup($context);
            
            return new JsonResponse([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cleanup(Request $request, Context $context): JsonResponse
    {
        try {
            $dryRun = $request->request->getBoolean('dryRun', false);
            
            $results = $this->cleanupService->runCleanup($context, $dryRun, 'manual');
            
            return new JsonResponse([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logs(Request $request, Context $context): JsonResponse
    {
        try {
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 25);
            
            $criteria = new Criteria();
            $criteria->setLimit($limit);
            $criteria->setOffset(($page - 1) * $limit);
            $criteria->addSorting(new FieldSorting('runAt', FieldSorting::DESCENDING));
            
            $logs = $this->cleanupLogRepository->search($criteria, $context);
            
            $logData = [];
            foreach ($logs->getElements() as $log) {
                $logData[] = [
                    'id' => $log->getId(),
                    'runAt' => $log->getRunAt()->format('Y-m-d H:i:s'),
                    'trigger' => $log->getTrigger(),
                    'mode' => $log->getMode(),
                    'results' => $log->getResults()
                ];
            }
            
            return new JsonResponse([
                'success' => true,
                'data' => [
                    'items' => $logData,
                    'total' => $logs->getTotal()
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
