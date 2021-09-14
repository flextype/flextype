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

class EntriesUpdateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('entries:update');
        $this->setDescription('Update entry.');
        $this->addOption('id', null, InputOption::VALUE_REQUIRED, 'Unique identifier of the entry.');
        $this->addOption('data', null, InputOption::VALUE_OPTIONAL, 'Data to update for the entry.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $data = $input->getOption('data') ? serializers()->json()->decode($input->getOption('data')) : [];

        if (entries()->update($input->getOption('id'), $data)) {
            $io->success('Entry ' . $input->getOption('id') . ' updated.');
            return Command::SUCCESS;
        } else {
            $io->error('Entry ' . $input->getOption('id') . ' wasn\'t updated.');
            return Command::FAILURE;
        }
    }
}