<?php declare(strict_types=1);

namespace IctDataCleanerPro\Command;

use IctDataCleanerPro\Service\Cleanup\CategoryCleanupHandler;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ict:cleanup:category',
    description: 'Run category cleanup with options'
)]
class CategoryCleanupCommand extends Command
{
    private CategoryCleanupHandler $handler;

    public function __construct(CategoryCleanupHandler $handler)
    {
        parent::__construct();
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('emptycategories', null, InputOption::VALUE_NONE, 'Clean up empty categories')
            ->addOption('nosalesinmonths', null, InputOption::VALUE_REQUIRED, 'Clean categories with no product sales in X months')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview only, do not delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createDefaultContext();
        $dryRun = (bool) $input->getOption('dry-run');
        $handled = false;

        $config = [];

        if ($input->getOption('emptycategories')) {
            $output->writeln(['<info>Cleaning empty categories</info>']);
            $config['categoryCleanup.emptyCategories'] = true;
            $handled = true;
        }

        $months = $input->getOption('nosalesinmonths');
        if (is_numeric($months)) {
            $intMonths = (int) $months;
            $output->writeln(["<info>Cleaning categories with no sales in {$intMonths} months</info>"]);
            $config['categoryCleanup.noSalesMonths'] = $intMonths;
            $handled = true;
        }

        if (!$handled) {
            $output->writeln([
                "<error>No cleanup option provided. Use at least one:</error>",
                "  --emptycategories",
                "  --nosalesinmonths=<months>",
            ]);
            return Command::INVALID;
        }

        $result = $this->handler->cleanup($config, $dryRun, $context);
        $output->writeln([json_encode($result, JSON_PRETTY_PRINT)]);
        $output->writeln(['<info>✅ Cleanup process completed.</info>']);

        return Command::SUCCESS;
    }
}
