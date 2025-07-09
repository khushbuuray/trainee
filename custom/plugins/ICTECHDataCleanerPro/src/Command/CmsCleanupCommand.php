<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Command;

use ICTECHDataCleanerPro\Service\Cleanup\CmsCleanupHandler;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ictech:cleanup:cms',
    description: 'Cleanup unpublished CMS drafts older than given months'
)]
class CmsCleanupCommand extends Command
{
    private CmsCleanupHandler $cleanupHandler;

    public function __construct(CmsCleanupHandler $cleanupHandler)
    {
        parent::__construct();
        $this->cleanupHandler = $cleanupHandler;
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'olderthanmonths',
                null,
                InputOption::VALUE_REQUIRED,
                'Delete unpublished CMS drafts older than X months'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Preview without deleting anything'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $monthsOption = $input->getOption('olderthanmonths');
        $dryRun = (bool) $input->getOption('dry-run');

        if (!is_numeric($monthsOption) || (int)$monthsOption <= 0) {
            $output->writeln(['<error>Please provide a valid --olderthanmonths value greater than 0</error>']);
            return Command::FAILURE;
        }

        $months = (int) $monthsOption;
        $output->writeln(["Cleaning unpublished CMS drafts older than {$months} months"]);

        $context = Context::createDefaultContext();

        $config = [
            'cmsPageCleanup.unpublishedDraftsMonths' => $months,
        ];

        $result = $this->cleanupHandler->cleanup($config, $dryRun, $context);
        $drafts = $result['items']['unpublished_drafts'] ?? ['count' => 0, 'sample' => []];

        $output->writeln([json_encode([
            'count' => $drafts['count'],
            'sample' => $drafts['sample'],
        ], JSON_PRETTY_PRINT)]);

        $output->writeln(['<info>✅ Cleanup process completed.</info>']);
        return Command::SUCCESS;
    }
}
