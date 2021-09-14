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

class EntriesCreateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('entries:create');
        $this->setDescription('Create entry.');
        $this->addOption('id', null, InputOption::VALUE_REQUIRED, 'Unique identifier of the entry.');
        $this->addOption('data', null, InputOption::VALUE_OPTIONAL, 'Data to create for the entry.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $data = $input->getOption('data') ? serializers()->json()->decode($input->getOption('data')) : [];

        if (entries()->create($input->getOption('id'), $data)) {
            $io->success('Entry ' . $input->getOption('id') . ' created.');
            return Command::SUCCESS;
        } else {
            $io->error('Entry ' . $input->getOption('id') . ' wasn\'t created.');
            return Command::FAILURE;
        }
    }
}