<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Command;

use ICTECHDataCleanerPro\Service\Cleanup\CustomerCleanupHandler;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ictech:cleanup:customer',
    description: 'Run customer cleanup with options'
)]
class CustomerCleanupCommand extends Command
{
    private CustomerCleanupHandler $handler;

    public function __construct(CustomerCleanupHandler $handler)
    {
        parent::__construct();
        $this->handler = $handler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('guestinactiveinmonths', null, InputOption::VALUE_REQUIRED, 'Delete guest users inactive for X months')
            ->addOption('inactivecustomerinmonths', null, InputOption::VALUE_REQUIRED, 'Delete inactive registered customers older than X months')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview only, do not delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createDefaultContext();
        $dryRun = (bool) $input->getOption('dry-run'); // ✅ Ensures correct type
        $handled = false;

        $guestOption = $input->getOption('guestinactiveinmonths');
        if (is_numeric($guestOption)) {
            $months = (int) $guestOption;
            $output->writeln(["<info>Cleaning guest customers inactive for {$months} months</info>"]);
            $result = $this->handler->cleanupGuestCustomers($months, $dryRun, $context);
            $output->writeln([json_encode($result, JSON_PRETTY_PRINT)]);
            $handled = true;
        }

        $inactiveOption = $input->getOption('inactivecustomerinmonths');
        if (is_numeric($inactiveOption)) {
            $months = (int) $inactiveOption;
            $output->writeln(["<info>Cleaning inactive registered customers older than {$months} months</info>"]);
            $result = $this->handler->cleanupInactiveCustomers($months, $dryRun, $context);
            $output->writeln([json_encode($result, JSON_PRETTY_PRINT)]);
            $handled = true;
        }

        if (!$handled) {
            $output->writeln([
                "<error>No cleanup option provided. Use at least one:</error>",
                "  --guestinactiveinmonths=<months>",
                "  --inactivecustomerinmonths=<months>",
            ]);
            return Command::INVALID;
        }

        $output->writeln(['<info>✅ Cleanup process completed.</info>']);
        return Command::SUCCESS;
    }
}
