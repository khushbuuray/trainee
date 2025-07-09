<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Command;

use ICTECHDataCleanerPro\Service\Cleanup\ReviewCleanupHandler;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ictech:cleanup:review',
    description: 'Cleanup unapproved product reviews older than a given number of days'
)]
class ReviewCleanupCommand extends Command
{
    private ReviewCleanupHandler $reviewCleanupHandler;

    public function __construct(ReviewCleanupHandler $reviewCleanupHandler)
    {
        parent::__construct();
        $this->reviewCleanupHandler = $reviewCleanupHandler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('unapproveddays', null, InputOption::VALUE_REQUIRED, 'Unapproved review age in days')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simulate cleanup without deleting data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $daysOption = $input->getOption('unapproveddays');

        if (!is_numeric($daysOption) || (int) $daysOption <= 0) {
            $output->writeln('<error>Please provide a valid --unapproveddays value greater than 0</error>');
            return Command::INVALID;
        }

        $days = (int) $daysOption;
        $dryRun = (bool) $input->getOption('dry-run');

        $output->writeln(["Cleaning unapproved product reviews older than {$days} days"]);

        $context = Context::createDefaultContext();
        $result = $this->reviewCleanupHandler->cleanup([
            'reviewCleanup.unapprovedDays' => $days,
        ], $dryRun, $context);

        $output->writeln([json_encode($result['items']['unapproved_reviews'], JSON_PRETTY_PRINT)]);
        $output->writeln(['<info>✅ Cleanup process completed.</info>']);
        return Command::SUCCESS;
    }
}
