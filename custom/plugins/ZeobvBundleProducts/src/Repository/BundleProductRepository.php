<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Repository;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Zeobv\BundleProducts\Struct\BundleProduct\BundleConnectionData;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Zeobv\BundleProducts\Extension\Content\Product\BundleProductExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Zeobv\BundleProducts\Core\Content\Product\Aggregate\ProductBundleConnection\ProductBundleConnectionEntity;
use Zeobv\BundleProducts\Core\Content\Product\Aggregate\ProductBundleConnection\ProductBundleConnectionCollection;

class BundleProductRepository
{
    public const CONTEXT_STATE_BUNDLE_PRODUCT_LOAD = 'bundle_product_load';

    public function __construct(
        private readonly EntityRepository $productRepository,
        private readonly EntityRepository $bundleConnectionRepository,
        private readonly SalesChannelRepository $salesChannelProductRepository,
        private readonly Connection $connection
    ) {
    }

    public function getBundleProductContents(array $productIds, Context $context): array
    {
        $productCount = count($productIds);

        if ($productCount < 1) {
            return [];
        }

        $criteria = new Criteria($productIds);
        $criteria->setLimit($productCount);
        $criteria->setTitle('Load bundle product contents');

        $criteria->addFilter(new NotFilter(NotFilter::CONNECTION_AND, [
            new EqualsFilter(BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.id', null),
        ]));

        $criteria->addAssociations([
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.prices',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.manufacturer',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.cover',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.visibilities',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.options',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.options.group',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.children',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.children.prices',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.children.options',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.children.options.group',
            BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME . '.deliveryTime',
        ]);

        $context->setConsiderInheritance(true);
        $context->addExtension('zeobvBundleProductContext', new ArrayStruct([
            'productIds' => $productIds,
        ]));
        $context->addState(self::CONTEXT_STATE_BUNDLE_PRODUCT_LOAD);

        $result = $this->productRepository->search($criteria, $context);

        $context->removeState(self::CONTEXT_STATE_BUNDLE_PRODUCT_LOAD);

        $groupedConnectionData = $this->getProductBundleConnectionsForBundleProducts($result->getIds());

        $groupedProductData = [];

        /** @var ProductEntity $bundleProduct */
        foreach ($result->getElements() as $bundleProduct) {
            /** @var ProductCollection|null $bundleContents */
            $bundleContents = $bundleProduct->getExtension(BundleProductExtension::BUNDLE_CONTENTS_EXTENSION_NAME);

            if (!is_iterable($bundleContents)) {
                continue;
            }

            $preparedBundleContents = [];

            foreach ($bundleContents as $productEntity) {
                if (!isset($groupedConnectionData[$bundleProduct->getId()][$productEntity->getId()])) {
                    continue;
                }

                // Break reference
                $bundleProductItem = ProductEntity::createFrom($productEntity);
                $bundleProductItem->addExtension(
                    BundleConnectionData::EXTENSION_NAME,
                    $groupedConnectionData[$bundleProduct->getId()][$productEntity->getId()]
                );

                $preparedBundleContents[] = $bundleProductItem;
            }

            $groupedProductData[$bundleProduct->getId()] = new ProductCollection($preparedBundleContents);
        }

        return $groupedProductData;
    }

    public function getBundleProductsForProductId(string $productId, int $limit, SalesChannelContext $context): ?EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->setLimit($limit);
        $criteria->addFilter(new EqualsFilter('productId', $productId));

        /** @var ProductBundleConnectionCollection $bundleConnections */
        $bundleConnections = $this->bundleConnectionRepository->search(
            $criteria,
            $context->getContext()
        )->getEntities();

        $productIds = array_values($bundleConnections->map(static function (ProductBundleConnectionEntity $bundleConnection) {
            return $bundleConnection->getBundleProductId();
        }));

        if ($productIds === []) {
            return null;
        }

        $criteria = new Criteria();
        $criteria->setIds($productIds);
        $criteria->addAssociation('option.group')
            ->addAssociation('option.media')
            ->addAssociation('media')->addAssociation('cover');

        return $this->salesChannelProductRepository->search(
            $criteria,
            $context
        );
    }

    private function getProductBundleConnectionsForBundleProducts(array $bundleProductIds): array
    {
        $bundleConnections = $this->connection->executeQuery(
            'SELECT LOWER(HEX(id)) as id,
            LOWER(HEX(bundle_product_id)) as bundleProductId,
            LOWER(HEX(product_id)) as productId,
            `position` as position,
            `quantity` as quantity,
            `modifiable` as modifiable,
            `optional` as optional,
            `comment` as comment
            FROM zeobv_product_bundle_connection
            WHERE bundle_product_id IN (:bundleProductIds)
            ORDER BY bundle_product_id',
            [
                'bundleProductIds' => Uuid::fromHexToBytesList($bundleProductIds),
            ],
            [
                'bundleProductIds' => Connection::PARAM_STR_ARRAY,
            ]
        )->fetchAllAssociative();

        $groupedConnectionData = [];
        foreach ($bundleConnections as $connection) {
            $bundleProductId = $connection['bundleProductId'];
            $productId = $connection['productId'];

            if (!isset($groupedConnectionData[$bundleProductId])) {
                $groupedConnectionData[$bundleProductId] = [];
            }

            $groupedConnectionData[$bundleProductId][$productId] = new BundleConnectionData(
                $connection['id'],
                $bundleProductId,
                $productId,
                intval($connection['quantity']),
                intval($connection['position']),
                boolval($connection['modifiable']),
                boolval($connection['optional']),
                $connection['comment']
            );
        }

        return $groupedConnectionData;
    }
}
