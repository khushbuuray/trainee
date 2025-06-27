<?php declare(strict_types=1);

namespace IctDataCleanerPro\Command;

use IctDataCleanerPro\Service\Cleanup\CartCleanupHandler;
use IctDataCleanerPro\Service\Cleanup\OrderCleanupHandler;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ict:cleanup:order-cart',
    description: 'Run cleanup for abandoned carts, cancelled orders, and old transactions'
)]
class OrderCartCleanupCommand extends Command
{
    private CartCleanupHandler $cartHandler;
    private OrderCleanupHandler $orderHandler;

    public function __construct(CartCleanupHandler $cartHandler, OrderCleanupHandler $orderHandler)
    {
        parent::__construct();
        $this->cartHandler = $cartHandler;
        $this->orderHandler = $orderHandler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('abandonedcartolderthandays', null, InputOption::VALUE_REQUIRED, 'Delete carts older than X days')
            ->addOption('cancelledorderolderthanmonths', null, InputOption::VALUE_REQUIRED, 'Delete cancelled orders older than X months')
            ->addOption('transactionolderthanmonths', null, InputOption::VALUE_REQUIRED, 'Delete transactions older than X months')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview only, do not delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createDefaultContext();
        $dryRun = (bool) $input->getOption('dry-run');
        $handled = false;

        // Abandoned Cart Cleanup
        $daysOption = $input->getOption('abandonedcartolderthandays');
        if (is_numeric($daysOption)) {
            $days = (int) $daysOption;
            $output->writeln(["<info>Cleaning abandoned carts older than {$days} days</info>"]);
            $config = ['cartCleanup.abandonedDays' => $days];
            $result = $this->cartHandler->cleanup($config, $dryRun, $context);
            $output->writeln([json_encode($result, JSON_PRETTY_PRINT)]);
            $handled = true;
        }

        // Cancelled Orders Cleanup
        $cancelledMonthsOption = $input->getOption('cancelledorderolderthanmonths');
        if (is_numeric($cancelledMonthsOption)) {
            $months = (int) $cancelledMonthsOption;
            $output->writeln(["<info>Cleaning cancelled orders older than {$months} months</info>"]);
            $config = ['orderCleanup.cancelledAgeMonths' => $months];
            $result = $this->orderHandler->cleanup($config, $dryRun, $context);
            $output->writeln([json_encode($result, JSON_PRETTY_PRINT)]);
            $handled = true;
        }

        // Transaction Cleanup
        $transactionMonthsOption = $input->getOption('transactionolderthanmonths');
        if (is_numeric($transactionMonthsOption)) {
            $months = (int) $transactionMonthsOption;
            $output->writeln(["<info>Cleaning transactions older than {$months} months</info>"]);
            $config = ['transactionCleanup.ageMonths' => $months];
            $result = $this->orderHandler->cleanup($config, $dryRun, $context);
            $output->writeln([json_encode($result, JSON_PRETTY_PRINT)]);
            $handled = true;
        }

        if (!$handled) {
            $output->writeln(['<error>No cleanup option provided. Use at least one:</error>']);
            $output->writeln([
                '  --abandonedcartolderthandays=<days>',
                '  --cancelledorderolderthanmonths=<months>',
                '  --transactionolderthanmonths=<months>',
            ]);
            return Command::INVALID;
        }

        $output->writeln(['<info>✅ Cleanup process completed.</info>']);
        return Command::SUCCESS;
    }
}
