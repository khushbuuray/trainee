<?php declare(strict_types=1);

namespace IctDataCleanerPro\Controller\StoreApi;

use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use IctDataCleanerPro\Service\CleanupService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

/**
 * @RouteScope(scopes={"store-api"})
 */
class CleanupController
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
     * @Route("/store-api/ict-data-cleaner/preview", name="store-api.ict_data_cleaner.preview", methods={"POST"})
     */
    public function preview(Request $request, SalesChannelContext $context): JsonResponse
    {
        $results = $this->cleanupService->previewCleanup($context->getContext());
        
        return new JsonResponse([
            'success' => true,
            'data' => $results
        ]);
    }
}
