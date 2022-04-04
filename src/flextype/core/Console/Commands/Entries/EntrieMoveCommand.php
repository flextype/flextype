<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console\Commands\Entries;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EntriesMoveCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('entries:move');
        $this->setDescription('Move entry.');
        $this->addArgument('id', InputArgument::REQUIRED, 'Unique identifier of the entry.');
        $this->addArgument('newID', InputArgument::REQUIRED, 'New Unique identifier of the entry');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (entries()->move($input->getArgument('id'), $input->getArgument('newID'))) {
            $io->success('Entry ' . $input->getArgument('id') . ' moved to ' . $input->getArgument('newID'));
            return Command::SUCCESS;
        } else {
            $io->error('Entry ' . $input->getArgument('id') . ' wasn\'t moved to ' . $input->getArgument('newID'));
            return Command::FAILURE;
        }
    }
}