<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console\Commands\Cache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;

class CacheSetMultipleCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:set-multiple');
        $this->setDescription('Set multiple items.');
        $this->addArgument('values', InputArgument::REQUIRED, 'Values.');
        $this->addArgument('ttl', InputArgument::OPTIONAL, 'Time To Live.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $values = $input->getArgument('values') ? serializers()->json()->decode($input->getArgument('values')) : [];
        $ttl    = $input->getArgument('ttl') ?? 300;

        if (cache()->setMultiple($values, $ttl)) {
            $io->success('Cache items with keys ' . implode(', ', array_keys($values)) . ' create.');
            return Command::SUCCESS;
        } else {
            $io->error('Cache items with keys ' . implode(', ', array_keys($values))  . ' wasn\'t created.');
            return Command::FAILURE;
        }
    }
}