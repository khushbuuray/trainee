<?php declare(strict_types=1);

namespace IctDataCleanerPro\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use IctDataCleanerPro\Service\CleanupService;
use IctDataCleanerPro\Service\Cleanup\ProductCleanupHandler;
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
use IctDataCleanerPro\Service\Cleanup\CleanupHandlerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;


#[Route('/api/ict-data-cleaner', name: 'api.ict_data_cleaner', defaults: ['_routeScope' => ['api']])]
class CleanupController extends AbstractController
{
    /** @var array<string, CleanupHandlerInterface> */
    private array $handlers;

    private SystemConfigService $systemConfigService;
    private CleanupService $cleanupService;

    public function __construct(
        CleanupService $cleanupService,
        SystemConfigService $systemConfigService,
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

        $this->handlers = [
            'productCleanup'      => $productCleanupHandler,
            'customerCleanup'     => $customerCleanupHandler,
            'cartCleanup'         => $cartCleanupHandler,
            'orderCleanup'        => $orderCleanupHandler,
            'categoryCleanup'     => $categoryCleanupHandler,
            'promotionCleanup'    => $promotionCleanupHandler,
            'reviewCleanup'       => $reviewCleanupHandler,
            'cmsPageCleanup'      => $cmsCleanupHandler,
            'newsletterCleanup'   => $newsletterCleanupHandler,
            'mediaCleanup'        => $mediaCleanupHandler,
            'logCleanup'          => $logCleanupHandler,
            'transactionCleanup'  => $orderCleanupHandler,
            'cartRuleCleanup'     => $promotionCleanupHandler
        ];
    }

    #[Route(path: '/preview', name: 'api.ict_data_cleaner.preview', methods: ['POST'])]
    public function preview(Request $request, Context $context): JsonResponse
    {
        try {
            $config = json_decode($request->getContent(), true);
            if (!is_array($config)) {
                return new JsonResponse(['error' => 'Invalid config payload'], 400);
            }

            $cleanedConfig = [];
            foreach ($config as $key => $value) {
                $strippedKey = str_replace('IctDataCleaner.config.', '', (string)$key);
                $cleanedConfig[$strippedKey] = $value;
            }

            $firstKey = array_key_first($cleanedConfig);
            if (!is_string($firstKey)) {
                return new JsonResponse(['error' => 'Invalid config key structure'], 400);
            }

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
                'data'    => $result,
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    #[Route(path: '/{entity}/remove', name: 'api.ict_data_cleaner.entity_remove', methods: ['POST'])]
    public function remove(Request $request, Context $context): JsonResponse
    {
        try {
            $payload = json_decode($request->getContent(), true);
            if (!is_array($payload)) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid payload'], 400);
            }

            $matchedKey = null;
            foreach ($payload as $key => $value) {
                if (is_string($key) && str_ends_with($key, 'Ids')) {
                    $matchedKey = $key;
                    break;
                }
            }

            if (!$matchedKey || !isset($payload[$matchedKey])) {
                return new JsonResponse(['success' => false, 'message' => 'No valid ID key found in payload.'], 400);
            }

            $rawItems = $payload[$matchedKey];
            if (!is_iterable($rawItems)) {
                return new JsonResponse(['success' => false, 'message' => 'IDs must be iterable.'], 400);
            }

            $ids = [];
            foreach ($rawItems as $group) {
                foreach ((array)$group as $item) {
                    if (is_array($item) && isset($item['id']) && is_string($item['id'])) {
                        $ids[] = $item['id'];
                    } elseif (is_string($item)) {
                        $ids[] = $item;
                    }
                }
            }

            if (empty($ids)) {
                return new JsonResponse(['success' => false, 'message' => 'No valid IDs provided.'], 400);
            }

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
                'cartRuleCleanup'    => 'promotion', // ✅ ADD THIS LINE
            ];


            $entityKey = preg_replace('/Ids$/', '', $matchedKey);
            $entityGroup = explode('.', (string)$entityKey)[0];
            $entityName = $entityMap[$entityGroup] ?? null;

            if (!$entityName) {
                return new JsonResponse(['success' => false, 'message' => "Unknown entity group: {$entityGroup}"], 400);
            }

            $deletePayload = array_filter(array_map(function ($id) {
                return Uuid::isValid($id) ? ['id' => $id] : null;
            }, $ids));

            if (empty($deletePayload)) {
                return new JsonResponse(['success' => false, 'message' => 'No valid UUIDs to delete.'], 400);
            }

            /** @phpstan-var EntityRepository<EntityCollection<Entity>> $repository */
            $repository = $this->container->get($entityName . '.repository');


            $repository->delete($deletePayload, $context);

            return new JsonResponse(['success' => true, 'deleted' => count($deletePayload)]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    #[Route(path: '/cleanup/{module}', name: 'api.ict_data_cleaner.cleanup', methods: ['POST'])]
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
        } catch (\Throwable $th) {
            return new JsonResponse([
                'success' => false,
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ], 500);
        }
    }
}
