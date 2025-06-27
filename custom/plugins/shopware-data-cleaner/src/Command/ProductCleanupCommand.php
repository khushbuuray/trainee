<?php declare(strict_types=1);

namespace IctDataCleanerPro\Command;

use IctDataCleanerPro\Service\Cleanup\ProductCleanupHandler;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ict:cleanup:product',
    description: 'Run product cleanup with specific criteria'
)]
class ProductCleanupCommand extends Command
{
    private ProductCleanupHandler $handler;

    public function __construct(ProductCleanupHandler $handler)
    {
        parent::__construct();
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('neversoldinmonths', null, InputOption::VALUE_REQUIRED, 'Clean products not sold in X months')
            ->addOption('productneversold', null, InputOption::VALUE_NONE, 'Clean products never sold')
            ->addOption('inactiveproductsolderthanmonths', null, InputOption::VALUE_REQUIRED, 'Clean inactive products older than X months')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simulate cleanup without deleting anything');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createDefaultContext();
        $dryRun = (bool) $input->getOption('dry-run');

        $config = [];
        $hasCleanupOption = false;

        $neverSoldInMonthsOption = $input->getOption('neversoldinmonths');
        if (is_numeric($neverSoldInMonthsOption)) {
            $months = (int) $neverSoldInMonthsOption;
            if ($months <= 0) {
                $output->writeln('<error>--neversoldinmonths must be greater than 0</error>');
                return Command::INVALID;
            }
            $config['productCleanup.monthsNotSold'] = $months;
            $output->writeln(["Cleaning products not sold in last {$months} months"]);
            $hasCleanupOption = true;
        }

        if ($input->getOption('productneversold')) {
            $config['productCleanup.deleteNeverSold'] = true;
            $output->writeln(['Cleaning products that were never sold']);
            $hasCleanupOption = true;
        }

        $inactiveOlderOption = $input->getOption('inactiveproductsolderthanmonths');
        if (is_numeric($inactiveOlderOption)) {
            $months = (int) $inactiveOlderOption;
            if ($months <= 0) {
                $output->writeln('<error>--inactiveproductsolderthanmonths must be greater than 0</error>');
                return Command::INVALID;
            }
            $config['productCleanup.monthsDisabled'] = $months;
            $output->writeln(["Cleaning inactive products older than {$months} months"]);
            $hasCleanupOption = true;
        }

        if (!$hasCleanupOption) {
            $output->writeln('<error>No cleanup option provided. Use at least one:</error>');
            $output->writeln([
                '  --neversoldinmonths=<months>',
                '  --productneversold',
                '  --inactiveproductsolderthanmonths=<months>',
            ]);
            return Command::INVALID;
        }

        $result = $this->handler->cleanup($config, $dryRun, $context);
        $output->writeln([json_encode($result, JSON_PRETTY_PRINT)]);
        $output->writeln(['<info>✅ Cleanup process completed.</info>']);

        return Command::SUCCESS;
    }
}
