<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console\Commands\Entries;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;

class EntriesCreateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('entries:create');
        $this->setDescription('Create entry.');
        $this->addArgument('id', InputArgument::REQUIRED, 'Unique identifier of the entry.');
        $this->addArgument('data', InputArgument::OPTIONAL, 'Data to create for the entry.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $data = $input->getArgument('data') ? serializers()->json()->decode($input->getArgument('data')) : [];

        if (entries()->create($input->getArgument('id'), $data)) {
            $io->success('Entry ' . $input->getArgument('id') . ' created.');
            return Command::SUCCESS;
        } else {
            $io->error('Entry ' . $input->getArgument('id') . ' wasn\'t created.');
            return Command::FAILURE;
        }
    }
}