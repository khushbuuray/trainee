<?php declare(strict_types=1);

namespace IctDataCleanerPro\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use IctDataCleanerPro\Service\CleanupService;
use Shopware\Core\Framework\Context;

class CleanupCommand extends Command
{
    /** @var string */
    protected static string $defaultName = 'ict:cleanup:run';

    private CleanupService $cleanupService;

    public function __construct(CleanupService $cleanupService)
    {
        parent::__construct(self::$defaultName);
        $this->cleanupService = $cleanupService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Run data cleanup')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run in dry-run mode')
            ->addOption('schedule', null, InputOption::VALUE_NONE, 'Run as scheduled task');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('ICT Data Cleanup Pro');

        $dryRun = (bool) $input->getOption('dry-run'); // ✅ Cast to bool
        $trigger = $input->getOption('schedule') ? 'scheduled' : 'cli';

        $io->info(sprintf('Running cleanup in %s mode', $dryRun ? 'DRY-RUN' : 'REAL'));

        try {
            $results = $this->cleanupService->runCleanup(
                Context::createDefaultContext(),
                $dryRun,
                $trigger
            );

            $this->displayResults($io, $results);

            $io->success('Cleanup completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Cleanup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * @param array<string, array{
     *     name?: string,
     *     items?: array<string, array{
     *         count?: int,
     *         sample?: array<int, array<string, string|null>>
     *     }>
     * }> $results
     */
    private function displayResults(SymfonyStyle $io, array $results): void
    {
        foreach ($results as $handlerClass => $handlerResults) {
            $io->section($handlerResults['name'] ?? $handlerClass);

            if (isset($handlerResults['items'])) {
                foreach ($handlerResults['items'] as $itemType => $itemResults) {
                    $io->writeln(sprintf(
                        '  %s: %d items',
                        str_replace('_', ' ', ucfirst($itemType)),
                        $itemResults['count'] ?? 0
                    ));

                    if (!empty($itemResults['sample'])) {
                        $io->writeln('  Sample items:');
                        foreach ($itemResults['sample'] as $sample) {
                            $io->writeln(sprintf(
                                '    - %s (%s)',
                                $sample['name'] ?? $sample['productNumber'] ?? 'N/A',
                                $sample['id'] ?? 'N/A'
                            ));
                        }
                    }
                }
            }
        }
    }
}
