<?php declare(strict_types=1);

namespace IctDataCleanerPro\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use IctDataCleanerPro\Service\CleanupService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use IctDataCleanerPro\Service\Cleanup\ProductCleanupHandler;
use Symfony\Component\Routing\Annotation\Route;
use IctDataCleanerPro\Service\Cleanup\CustomerCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\CartCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\OrderCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\CategoryCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\PromotionCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\ReviewCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\CmsCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\NewsletterCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\MediaCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\LogCleanupHandler;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
#[Route('/api/ict-data-cleaner', name: 'api.ict_data_cleaner', defaults: ['_routeScope' => ['api']])]

class CleanupController extends AbstractController
{
    // private CleanupService $cleanupService;
    // private EntityRepository $cleanupLogRepository;
    // private ProductCleanupHandler $productCleanupHandler;
    private array $handlers;
    private SystemConfigService $systemConfigService;



    public function __construct(
        CleanupService $cleanupService,
        SystemConfigService $systemConfigService,
        EntityRepository $cleanupLogRepository,
        ProductCleanupHandler $productCleanupHandler,
        CustomerCleanupHandler $customerCleanupHandler,
        CartCleanupHandler $cartCleanupHandler,
        OrderCleanupHandler $orderCleanupHandler,
        CategoryCleanupHandler $categoryCleanupHandler,
        PromotionCleanupHandler $promotionCleanupHandler,
        ReviewCleanupHandler $reviewCleanupHandler,
        CmsCleanupHandler $cmsCleanupHandler,
        NewsletterCleanupHandler $newsletterCleanupHandler,
        MediaCleanupHandler $mediaCleanupHandler,
        LogCleanupHandler $logCleanupHandler
        
    ) {
        $this->cleanupService = $cleanupService;
        $this->systemConfigService = $systemConfigService;

        // $this->cleanupLogRepository = $cleanupLogRepository;
        // $this->productCleanupHandler = $productCleanupHandler;
          $this->handlers = [
        'productCleanup' => $productCleanupHandler,
        'customerCleanup' => $customerCleanupHandler,
        'cartCleanup' => $cartCleanupHandler,
        'orderCleanup' => $orderCleanupHandler,
        'categoryCleanup' => $categoryCleanupHandler,
        'promotionCleanup' => $promotionCleanupHandler,
        'reviewCleanup' => $reviewCleanupHandler,
        'cmsPageCleanup' => $cmsCleanupHandler,
        'newsletterCleanup' => $newsletterCleanupHandler,
        'mediaCleanup' => $mediaCleanupHandler,
        'logCleanup' => $logCleanupHandler,
        'transactionCleanup' => $orderCleanupHandler,
        'cartRuleCleanup' => $promotionCleanupHandler
    ];
    }

    #[Route(path: '/preview', name: 'api.ict_data_cleaner.preview', methods: ['POST'])]
public function preview(Request $request, Context $context): JsonResponse
{
    try {
        $config = json_decode($request->getContent(), true);
         if (empty($config) || !is_array($config)) {
            return new JsonResponse(['error' => 'Invalid config payload'], 400);
        }
        $cleanedConfig = [];
        foreach ($config as $key => $value) {
            $strippedKey = str_replace('IctDataCleaner.config.', '', $key);
            $cleanedConfig[$strippedKey] = $value;
        }
        $firstKey = array_key_first($cleanedConfig); 
        $matchedHandler = null;
        foreach ($this->handlers as $key => $handler) {
            if (str_contains($firstKey, $key)) {
                $matchedHandler = $handler;
                break;
            }
        }

        if (!$matchedHandler) {
            return new JsonResponse(['error' => 'No handler matched for: ' . $firstKey], 400);
        }

        $result = $matchedHandler->cleanup($cleanedConfig, true, $context);
                return new JsonResponse([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
}
#[Route(path: '/{entity}/remove', name: 'api.ict_data_cleaner.entity_remove', methods: ['POST'])]
public function remove(Request $request, Context $context): JsonResponse
{
    try {
        $payload = json_decode($request->getContent(), true);
        // Step 1: Find the matched key
        $matchedKey = null;
        foreach ($payload as $key => $value) {
            if (str_ends_with($key, 'Ids')) {
                $matchedKey = $key;
                break;
            }
        }

        if (!$matchedKey) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No valid ID key found in payload.',
            ], 400);
        }

        // Step 2: Flatten grouped IDs
        $rawItems = $payload[$matchedKey];
        $ids = [];
        foreach ($rawItems as $group) {
            foreach ((array) $group as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $ids[] = $item['id'];
                } elseif (is_string($item)) {
                    $ids[] = $item;
                }
            }
        }
        if (empty($ids)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No IDs provided for deletion.',
            ], 400);
        }

        // Step 3: Entity name map
        $entityMap = [
            'productCleanup'     => 'product',
            'customerCleanup'    => 'customer',
            'cartCleanup'        => 'order',
            'categoryCleanup'    => 'category',
            'promotionCleanup'   => 'promotion',
            'reviewCleanup'      => 'product_review',
            'cmsPageCleanup'     => 'cms_page',
            'newsletterCleanup'  => 'newsletter_recipient',
            'mediaCleanup'       => 'media',
            'systemLogCleanup'   => 'log_entry',
            'orderCleanup'       => 'order',
            'transactionCleanup' => 'order',
        ];

        $entityKey = preg_replace('/Ids$/', '', $matchedKey);     
        $entityGroup = explode('.', $entityKey)[0];              

        $entityName = $entityMap[$entityGroup] ?? null;
        if (!$entityName) {
            return new JsonResponse([
                'success' => false,
                'message' => "Unknown entity group: {$entityGroup}",
            ], 400);
        }

        // Step 4: Delete using repository
        $deletePayload = array_filter(array_map(function ($id) {
            if (Uuid::isValid($id)) {
                return ['id' => $id];
            }
            return null;
        }, $ids));

        if (empty($deletePayload)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No valid IDS to delete.',
            ], 400);
        }
        $repository = $this->container->get($entityName . '.repository');
        $repository->delete($deletePayload, $context);
        return new JsonResponse([
            'success' => true,
            'deleted' => count($deletePayload),
        ]);

    } catch (\Throwable $e) {
        return new JsonResponse([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
}

#[Route(path: '/cleanup', name: 'api.ict_data_cleaner.cleanup', methods: ['POST'])]

 public function cleanup(Context $context, string $module): JsonResponse
    {
        try {
           $results = $this->cleanupService->runCleanup($context, false, 'scheduled', $module);
        $key = "IctDataCleanerPro.config.{$module}LastRun";

        $this->systemConfigService->set($key, (new \DateTime())->format(\DATE_ATOM));

        return new JsonResponse([
            'success' => true,
            'module'  => $module,
            'results' => $results,
        ]);
        } catch (\Exception $th) {
        }
        
    }

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
