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
use IctDataCleanerPro\Service\Cleanup\ProductCleanupHandler;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/ict-data-cleaner', name: 'api.ict_data_cleaner', defaults: ['_routeScope' => ['api']])]

class CleanupController extends AbstractController
{
    private CleanupService $cleanupService;
    private EntityRepository $cleanupLogRepository;
    private ProductCleanupHandler $productCleanupHandler;
    private SerializerInterface  $serializer;

    public function __construct(
        CleanupService $cleanupService,
        EntityRepository $cleanupLogRepository,
        ProductCleanupHandler $productCleanupHandler,
    ) {
        $this->cleanupService = $cleanupService;
        $this->cleanupLogRepository = $cleanupLogRepository;
        $this->productCleanupHandler = $productCleanupHandler;
    }

    #[Route(path: '/preview', name: 'api.ict_data_cleaner.preview', methods: ['POST'])]
public function preview(Request $request, Context $context): JsonResponse
{
    try {
        $config = json_decode($request->getContent(), true);
        $results = $this->productCleanupHandler->cleanup($config, false, $context);
        return new JsonResponse([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
}

#[Route(path: '/products/remove', name: 'api.ict_data_cleaner.products_remove', methods: ['POST'])]
public function remove(Request $request, Context $context): JsonResponse
{
    try {
        $payload = json_decode($request->getContent(), true);
        $productItems = $payload['productIds'] ?? [];

        if (empty($productItems)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No product IDs provided.'
            ], 400);
        }
         // Extract only the IDs
        $deleteData = array_map(function ($item) {
            return ['id' => $item['id']];
        }, array_values($productItems));

        $productRepository = $this->container->get('product.repository');
        // $productRepository->delete($deleteData, $context);

        return new JsonResponse([
            'success' => true,
            'deleted' => count($deleteData)
        ]);
    } catch (\Throwable $e) {
        dd($e);
        return new JsonResponse([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
}



    
    // #[Route(
    //     path: '/api/ict-data-cleaner/hey',
    //     name: 'api.ict_data_cleaner.hey',
    //     methods: ['GET']
    // )]
    // public function hey(): JsonResponse
    // {
    //     dd('hey');
    //     return new JsonResponse(['message' => 'Hey!']);
    // }

    // /**
    //  * @Route("/api/ict-data-cleaner/preview", name="api.ict_data_cleaner.preview", methods={"GET"})
    //  */
    // public function preview(Request $request, Context $context): JsonResponse
    // {
    //     try {
    //         $results = $this->cleanupService->previewCleanup($context);

    //         return new JsonResponse([
    //             'success' => true,
    //             'data' => $results
    //         ]);
    //     } catch (\Exception $e) {
    //         return new JsonResponse([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * @Route("/api/ict-data-cleaner/preview-products-not-sold", name="api.ict_data_cleaner.products_not_sold", methods={"GET", "POST"})
    //  */
    // public function previewProductsNotSoldIn(Request $request, Context $context): JsonResponse
    // {
    //     try {
    //         $months = (int) $request->get('months', 6);
    //         $config = ['productCleanup.monthsNotSold' => $months];
    //         $results = $this->productCleanupHandler->cleanup($config, false, $context);

    //         return new JsonResponse([
    //             'success' => true,
    //             'data' => $results['items']['products_not_sold']
    //         ]);
    //     } catch (\Exception $e) {
    //         return new JsonResponse([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * @Route("/api/ict-data-cleaner/cleanup", name="api.ict_data_cleaner.cleanup", methods={"POST"})
    //  */
    // public function cleanup(Request $request, Context $context): JsonResponse
    // {
    //     try {
    //         $dryRun = $request->request->getBoolean('dryRun', false);
    //         $results = $this->cleanupService->runCleanup($context, $dryRun, 'manual');

    //         return new JsonResponse([
    //             'success' => true,
    //             'data' => $results
    //         ]);
    //     } catch (\Exception $e) {
    //         return new JsonResponse([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * @Route("/api/ict-data-cleaner/logs", name="api.ict_data_cleaner.logs", methods={"GET"})
    //  */
    // public function logs(Request $request, Context $context): JsonResponse
    // {
    //     try {
    //         $page = $request->query->getInt('page', 1);
    //         $limit = $request->query->getInt('limit', 25);

    //         $criteria = new Criteria();
    //         $criteria->setLimit($limit);
    //         $criteria->setOffset(($page - 1) * $limit);
    //         $criteria->addSorting(new FieldSorting('runAt', FieldSorting::DESCENDING));

    //         $logs = $this->cleanupLogRepository->search($criteria, $context);

    //         $logData = [];
    //         foreach ($logs->getElements() as $log) {
    //             $logData[] = [
    //                 'id' => $log->getId(),
    //                 'runAt' => $log->getRunAt()->format('Y-m-d H:i:s'),
    //                 'trigger' => $log->getTrigger(),
    //                 'mode' => $log->getMode(),
    //                 'results' => $log->getResults()
    //             ];
    //         }

    //         return new JsonResponse([
    //             'success' => true,
    //             'data' => [
    //                 'items' => $logData,
    //                 'total' => $logs->getTotal()
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         return new JsonResponse([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
