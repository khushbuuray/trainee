<?php declare(strict_types=1);

namespace ICTECH_LimitedStockNotification\Storefront\Controller;

use DateTime;
use ICTECH_LimitedStockNotification\Core\Content\IctCartWishlist\IctCartWishlistEntity;
use ICTECH_LimitedStockNotification\Core\Content\IctCartWishlistProduct\IctCartWishlistProductEntity;
use ICTECH_LimitedStockNotification\Service\EmailService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
#[Package('storefront')]

class CartWishlistEmailController extends StorefrontController
{
    private SystemConfigService $systemConfigService;
    private EntityRepository $ictCartWishlistRepository;
    private EntityRepository $ictCartWishlistProductRepository;
    private EmailService $emailService;
    private EntityRepository $customerRepository;
    private EntityRepository $salesChannelRepository;

    public function __construct(
        SystemConfigService $systemConfigService,
        EntityRepository $ictCartWishlistRepository,
        EntityRepository $ictCartWishlistProductRepository,
        EmailService $emailService,
        EntityRepository $customerRepository,
        EntityRepository $salesChannelRepository,
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->ictCartWishlistRepository = $ictCartWishlistRepository;
        $this->ictCartWishlistProductRepository = $ictCartWishlistProductRepository;
        $this->emailService = $emailService;
        $this->customerRepository = $customerRepository;
        $this->salesChannelRepository = $salesChannelRepository;
    }

    public function cartWishlistEmail(Context $context): JsonResponse
    {
        $salesChannelCriteria = new Criteria();
        $salesChannels = $this->salesChannelRepository->search($salesChannelCriteria, $context)->getElements();
        
        foreach ($salesChannels as $salesChannel) {
            $salesChannelId = $salesChannel->getId();
        
            $active = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.active', $salesChannelId);
            $hours = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.ictCartWishlistDecay', $salesChannelId);
            $configStock = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.limitedStockAlert', $salesChannelId);
            $wishlistStatus = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.wishlistNotification', $salesChannelId);
            $delayConfigHours = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.ictCartWishlistTrash', $salesChannelId);

            if (! $hours == null) {
                $hours = $this->systemConfigService->get('ICTECH_LimitedStockNotification.config.ictCartWishlistDecay', $salesChannelId);
            }

            if ($hours == null or $hours == 0) {
                $hours = 24;
            }

            // Hours check
            if ($active) {
                $criteria = new Criteria();
                $criteria->addFilter(new EqualsFilter('mailSent', 0));
                $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
   
                $getCartWishlistData = $this->ictCartWishlistRepository->search($criteria, $context)->getElements();

                /** @var IctCartWishlistEntity $IctCartWishlist */
                foreach ($getCartWishlistData as $IctCartWishlist) {
                    $customerId = $IctCartWishlist->get('customerId');

                    $criteria = new Criteria();
                    $criteria->addFilter(new EqualsFilter('id', $customerId));

                    $customerData = $this->customerRepository->search($criteria, $context)->first();

                    $currentTimestamp = time();
                    $createdDate = $IctCartWishlist->get('createdAt');
                    $datetString = $createdDate->format('Y-m-d H:i:s');
                    $dateTime = new DateTime($datetString);
                    $timestamp = $dateTime->getTimestamp();
                    $hoursTimestamp = strtotime('+' . $hours . ' hours', $timestamp);

                    if ($currentTimestamp >= $hoursTimestamp) {
                        // For wishlist
                        if ($wishlistStatus === true) {
                            if ($IctCartWishlist->get('wishlistId') !== null) {
                                $productInfo = $IctCartWishlist->get('lineItemsWishlist');
                                //for wishlist product stock:
                                foreach ($productInfo as $key => $wishlistValue) {
                                    if ($configStock >= $wishlistValue['availableStock'] && $wishlistValue['availableStock'] > 0) {
                                        $this->emailService->sendEmailWishlist($productInfo, $context, $customerData, $IctCartWishlist->get('wishlistId'));
                                    }
                                }
                            }
                        }
                        // For cart
                        if ($IctCartWishlist->get('wishlistId') === null) {
                            $productInfo = $IctCartWishlist->get('lineItems');
                            //for cart product stock:
                            foreach ($productInfo as $key => $cartValue) {
                                if ($configStock >= $cartValue['deliveryInformation']['stock'] && $cartValue['deliveryInformation']['stock'] > 0) {
                                    $this->emailService->sendEmailCart($productInfo, $context, $customerData, $IctCartWishlist->get('cartToken'));
                                }
                            }
                        }
                    }

                    $criteria = new Criteria();
                    $criteria->addFilter(new EqualsFilter('mailSent', 1));
                    $criteria->addFilter(new EqualsFilter('customerId', $IctCartWishlist->getCustomerId()));
                    $getCartWishlistProductData = $this->ictCartWishlistProductRepository->search($criteria, $context)->getElements();

                    /** @var IctCartWishlistProductEntity $IctCartWishlistProduct */
                    foreach ($getCartWishlistProductData as $IctCartWishlistProduct) {
                        try {
                            $createdAt = $IctCartWishlistProduct->getCreatedAt();
                    
                            if (!$createdAt instanceof \DateTimeInterface) {
                                continue; // Skip invalid data
                            }
                    
                            $now = new \DateTimeImmutable();
                            $interval = $now->diff($createdAt);
                    
                            $totalHours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
                    
                            if ($totalHours > $delayConfigHours) {
                                $this->ictCartWishlistProductRepository->delete([
                                    ['id' => $IctCartWishlistProduct->getId()],
                                ], $context);
                            }
                        } catch (Throwable $e) {
                            $this->logOrThrowException($e);
                        }
                    }                    
                }
            }            
        }
        return new JsonResponse();
    }
}
