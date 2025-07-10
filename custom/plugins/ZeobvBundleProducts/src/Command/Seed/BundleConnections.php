<?php

declare(strict_types=1);

namespace Zeobv\BundleProducts\Command\Seed;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Zeobv\BundleProducts\Core\Content\Product\Aggregate\ProductBundleConnection\ProductBundleConnectionEntity;
use Zeobv\BundleProducts\Struct\BundleProduct\ProductBundle;

class BundleConnections extends Command
{
    protected EntityRepository $productRepository;
    protected EntityRepository $bundleConnectionRepository;
    protected string $environment;

    public function __construct(
        EntityRepository $productRepository,
        EntityRepository $bundleConnectionRepository,
        $name = null
    ) {
        $this->productRepository = $productRepository;
        $this->bundleConnectionRepository = $bundleConnectionRepository;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('zeo:bundle:seed');
        $this->setDescription('Debug command populating the zeobv_product_bundle_connection table with relations based on the existing product database.');
        $this->addOption(
            'limit',
            'l',
            InputOption::VALUE_REQUIRED,
            'Set the limit for the amount of products to be retrieved.',
            500
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $questionHelper = $this->getHelper('question');

        $question = new ConfirmationQuestion('Are you sure you want to seed Bundle connections based on your current product database? (Y/n)' . PHP_EOL, false);

        if (!$questionHelper->ask($input, $output, $question)) {
            $output->writeln('Terminated.');
            return Command::SUCCESS;
        }

        $criteria = new Criteria();
        $criteria->setLimit(10000);
        $bundleResult = $this->bundleConnectionRepository->search($criteria, Context::createDefaultContext());

        $productIdsToExclude = $bundleResult->fmap(static function (ProductBundleConnectionEntity $entity) {
            return $entity->getProductId();
        });

        $criteria = new Criteria();
        $criteria->setLimit(intval($input->getOption('limit')));
        if (count($productIdsToExclude) > 0) {
            $criteria->addFilter(new NotFilter(MultiFilter::CONNECTION_AND, [
                new EqualsAnyFilter('id', $productIdsToExclude),
            ]));
        }

        $result = $this->productRepository->searchIds(
            $criteria,
            Context::createDefaultContext()
        );

        $data = [];

        /** @var string[] $ids */
        $ids = array_values(
            $result->getIds()
        );
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsAnyFilter(
                'productId',
                $ids
            )
        );

        /** @var string $productId */
        foreach ($result->getIds() as $productId) {
            $criteria = new Criteria();
            $criteria->addFilter(new NotFilter(MultiFilter::CONNECTION_AND, [
                new EqualsFilter('id', $productId),
                new EqualsAnyFilter('id', $ids),
            ]));
            $criteria->setLimit(random_int(2, 20));

            $productsForBundle = $this->productRepository->search($criteria, Context::createDefaultContext());

            /** @var ProductEntity $productForBundle */
            foreach ($productsForBundle as $productForBundle) {
                if ($productForBundle->hasExtension(ProductBundle::EXTENSION_NAME)) {
                    continue;
                }

                $data[] = [
                    'id' => md5($productId . $productForBundle->getId()),
                    'bundleProductId' => $productId,
                    'productId' => $productForBundle->getId(),
                    'quantity' => random_int(1, 99),
                ];
            }
        }

        $this->bundleConnectionRepository->upsert($data, Context::createDefaultContext());

        $output->writeln('Done.');

        return Command::SUCCESS;
    }
}
