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

class CacheClearCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:clear');
        $this->setDescription('Completely empty the cache.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (cache()->clear()) {
            $io->success('Cache cleared.');
            return Command::SUCCESS;
        } else {
            $io->error('Cache wasn\'t cleared.');
            return Command::FAILURE;
        }
    }
}