<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console\Commands\Cache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheDeleteMultipleCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:delete-multiple');
        $this->setDescription('Delete mutiple items.');
        $this->addArgument('keys', InputArgument::REQUIRED, 'Keys.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $keys = $input->getArgument('keys') ? serializers()->json()->decode($input->getArgument('keys')) : [];

        if (cache()->deleteMultiple($keys)) {
            $io->success('Cache items with keys ' . implode(', ', $keys) . ' deleted.');
            return Command::SUCCESS;
        } else {
            $io->error('Cache items with keys ' . implode(', ', $keys) . ' wasn\'t deleted.');
            return Command::FAILURE;
        }
    }
}