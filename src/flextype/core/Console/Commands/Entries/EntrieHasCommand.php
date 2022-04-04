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

class EntriesHasCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('entries:has');
        $this->setDescription('Check whether entry exists.');
        $this->addArgument('id', InputArgument::REQUIRED, 'Unique identifier of the entry.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (entries()->has($input->getArgument('id'))) {
            $io->success('Entry ' . $input->getArgument('id') . ' exists');
            return Command::SUCCESS;
        } else {
            $io->error('Entry ' . $input->getArgument('id') . ' doesn\'t exists');
            return Command::FAILURE;
        }
    }
}