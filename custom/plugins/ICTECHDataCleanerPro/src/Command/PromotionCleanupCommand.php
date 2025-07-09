<?php declare(strict_types=1);

namespace ICTECHDataCleanerPro\Command;

use ICTECHDataCleanerPro\Service\Cleanup\PromotionCleanupHandler;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ictech:cleanup:promotion',
    description: 'Cleanup promotions: expired, unused vouchers, orphaned rules'
)]
class PromotionCleanupCommand extends Command
{
    private PromotionCleanupHandler $cleanupHandler;

    public function __construct(PromotionCleanupHandler $cleanupHandler)
    {
        parent::__construct();
        $this->cleanupHandler = $cleanupHandler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('expiredMonths', null, InputOption::VALUE_OPTIONAL, 'Clean expired promotions older than X months')
            ->addOption('unusedVoucherMonths', null, InputOption::VALUE_OPTIONAL, 'Clean unused vouchers older than X months')
            ->addOption('orphaned', null, InputOption::VALUE_NONE, 'Clean orphaned cart rules')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview changes without deleting anything');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hasExpired = $input->getOption('expiredMonths') !== null;
        $hasUnused = $input->getOption('unusedVoucherMonths') !== null;
        $hasOrphaned = $input->getOption('orphaned');

        if (!$hasExpired && !$hasUnused && !$hasOrphaned) {
            $output->writeln('<error>No cleanup option provided. Use at least one:</error>');
            $output->writeln([
                "  --expiredMonths=<months>",
                "  --unusedVoucherMonths=<months>",
                "  --orphaned",
            ]);
            return Command::INVALID;
        }

        $config = [];

        if ($hasExpired) {
            $expiredOption = $input->getOption('expiredMonths');
            if (!is_numeric($expiredOption) || (int) $expiredOption <= 0) {
                $output->writeln('<error>--expiredMonths must be a number greater than 0</error>');
                return Command::INVALID;
            }

            $expiredMonths = (int) $expiredOption;
            $output->writeln(["Cleaning expired promotions older than {$expiredMonths} months"]);
            $config['promotionCleanup.expiredMonths'] = $expiredMonths;
        }

        if ($hasUnused) {
            $unusedOption = $input->getOption('unusedVoucherMonths');
            if (!is_numeric($unusedOption) || (int) $unusedOption <= 0) {
                $output->writeln('<error>--unusedVoucherMonths must be a number greater than 0</error>');
                return Command::INVALID;
            }

            $unusedMonths = (int) $unusedOption;
            $output->writeln(["Cleaning unused vouchers older than {$unusedMonths} months"]);
            $config['promotionCleanup.unusedVoucherMonths'] = $unusedMonths;
        }

        if ($hasOrphaned) {
            $output->writeln(["Cleaning orphaned cart rules"]);
            $config['cartRuleCleanup.orphaned'] = true;
        }

        $dryRun = (bool) $input->getOption('dry-run');
        $context = Context::createDefaultContext();

        $result = $this->cleanupHandler->cleanup($config, $dryRun, $context);

        $output->writeln([json_encode($result, JSON_PRETTY_PRINT)]);
        $output->writeln(['<info>✅ Cleanup process completed.</info>']);

        return Command::SUCCESS;
    }
}
