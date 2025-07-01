<?php declare(strict_types=1);

namespace IctDataCleanerPro\Command;

use IctDataCleanerPro\Service\CleanupService;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ict:cleanup:run',
    description: 'Run Data Cleanup manually',
)]
class RunCleanupCommand extends Command
{
    private CleanupService $cleanupService;

    public function __construct(CleanupService $cleanupService)
    {
        parent::__construct();
        $this->cleanupService = $cleanupService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('module', InputArgument::OPTIONAL, 'Cleanup module to run (e.g., productCleanup, customerCleanup)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $moduleOption = $input->getArgument('module');
        $module = is_string($moduleOption) ? $moduleOption : null;

        $context = Context::createDefaultContext();
        $output->writeln(['<info>Running cleanup for module: ' . ($module ?? 'all') . '</info>']);

        try {
            $results = $this->cleanupService->runCleanup($context, false, 'cli', $module);

            foreach ($results as $handler => $result) {
                $output->writeln(["<comment>Handler: {$handler}</comment>"]);
                $output->writeln([json_encode($result, JSON_PRETTY_PRINT)]);
            }

            $output->writeln(['<info>Cleanup completed successfully.</info>']);
        } catch (\Throwable $e) {
            $output->writeln(['<error>Error: ' . $e->getMessage() . '</error>']);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
