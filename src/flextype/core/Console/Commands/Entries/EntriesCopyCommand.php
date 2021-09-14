<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console\Commands\Entries;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EntriesCopyCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('entries:copy');
        $this->setDescription('Copy entry.');
        $this->addOption('id', null, InputOption::VALUE_REQUIRED, 'Unique identifier of the entry.');
        $this->addOption('newID', null, InputOption::VALUE_REQUIRED, 'New Unique identifier of the entry');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (entries()->copy($input->getOption('id'), $input->getOption('newID'))) {
            $io->success('Entry ' . $input->getOption('id') . ' coppied to ' . $input->getOption('newID'));
            return Command::SUCCESS;
        } else {
            $io->error('Entry ' . $input->getOption('id') . ' wasn\'t coppied to ' . $input->getOption('newID'));
            return Command::FAILURE;
        }
    }
}