<?php

declare(strict_types=1);

namespace Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Console\Command;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\StoreConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TriggerPluginsCommand extends Command
{
    public function __construct(
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('ad0e702:trigger:plugins');
        $this->setDescription("This command will trigger execution of plugins to demo how their sort order influences their execution order.");

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;

        try {
            $output->writeln('<info>The demo plugins have been triggerred successfully.</info>');
            $output->writeln('<info>Please inspect the var/log/debug.log file.</info>');
        } catch (LocalizedException $e) {
            $output->writeln(sprintf(
                '<error>%s</error>',
                $e->getMessage()
            ));
            $exitCode = 1;
        }

        return $exitCode;
    }
}
