<?php declare(strict_types=1);

namespace IctDataCleanerPro\Command;

use IctDataCleanerPro\Service\Cleanup\NewsletterCleanupHandler;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ict:cleanup:newsletter',
    description: 'Cleanup bounced newsletter recipients older than given months'
)]
class NewsletterCleanupCommand extends Command
{
    private NewsletterCleanupHandler $newsletterCleanupHandler;

    public function __construct(NewsletterCleanupHandler $newsletterCleanupHandler)
    {
        parent::__construct();
        $this->newsletterCleanupHandler = $newsletterCleanupHandler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('bouncedmonths', null, InputOption::VALUE_REQUIRED, 'Remove bounced newsletter recipients older than X months')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simulate the cleanup without deleting any data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $monthsOption = $input->getOption('bouncedmonths');
        if (!is_numeric($monthsOption) || (int) $monthsOption <= 0) {
            $output->writeln(['']);
            $output->writeln(['<error>Please provide a valid --bouncedmonths value greater than 0</error>']);
            return Command::INVALID;
        }

        $months = (int) $monthsOption;
        $dryRun = (bool) $input->getOption('dry-run');

        $output->writeln(['']);
        $output->writeln([sprintf('Cleaning bounced newsletter recipients older than %d months', $months)]);
        $output->writeln(['']);

        $context = Context::createDefaultContext();

        $result = $this->newsletterCleanupHandler->cleanup([
            'newsletterCleanup.bouncedMonths' => $months
        ], $dryRun, $context);

        $output->writeln([json_encode($result['items']['bounced_recipients'], JSON_PRETTY_PRINT)]);
        $output->writeln(['<info>✅ Cleanup process completed.</info>']);

        return Command::SUCCESS;
    }
}
