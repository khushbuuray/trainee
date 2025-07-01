<?php declare(strict_types=1);

namespace IctDataCleanerPro\Command;

use IctDataCleanerPro\Service\Cleanup\MediaCleanupHandler;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ict:cleanup:media',
    description: 'Cleans up orphaned media and/or thumbnails'
)]
class MediaCleanupCommand extends Command
{
    private MediaCleanupHandler $mediaCleanupHandler;

    public function __construct(MediaCleanupHandler $mediaCleanupHandler)
    {
        parent::__construct();
        $this->mediaCleanupHandler = $mediaCleanupHandler;
    }

    protected function configure(): void
    {
        $this
            ->addOption('orphanagedays', null, InputOption::VALUE_REQUIRED, 'Delete orphaned media older than X days')
            ->addOption('deletethumbnails', null, InputOption::VALUE_NONE, 'Delete orphaned thumbnails')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview only, do not delete anything');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createDefaultContext();
        $config = [];

        $orphanDaysOption = $input->getOption('orphanagedays');
        if (!is_numeric($orphanDaysOption) || (int) $orphanDaysOption <= 0) {
            $output->writeln(['<error>Please provide a valid --orphanagedays value greater than 0</error>']);
            return Command::INVALID;
        }

        $days = (int) $orphanDaysOption;
        $config['mediaCleanup.orphanAgeDays'] = $days;

        if ($input->getOption('deletethumbnails')) {
            $config['mediaCleanup.deleteThumbnails'] = true;
        }

        $dryRun = (bool) $input->getOption('dry-run');

        $output->writeln(["Running media cleanup with options: " . json_encode($config)]);

        $results = $this->mediaCleanupHandler->cleanup($config, $dryRun, $context);

        $flat = [];
        foreach ($results['items'] as $type => $data) {
            $flat[] = [
                'type' => $type,
                'count' => $data['count'],
                'sample' => $data['sample'],
            ];
        }

        $output->writeln([json_encode($flat, JSON_PRETTY_PRINT)]);
        $output->writeln(['<info>✅ Cleanup process completed.</info>']);
        return self::SUCCESS;
    }
}
