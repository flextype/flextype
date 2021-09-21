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
use Symfony\Component\Console\Input\InputArgument;

class CacheDeleteCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:delete');
        $this->setDescription('Delete item.');
        $this->addArgument('key', InputArgument::REQUIRED, 'Key.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = $input->getArgument('key');

        if (cache()->delete($key)) {
            $io->success('Cache item with key ' . $key . ' deleted.');
            return Command::SUCCESS;
        } else {
            $io->error('Cache item with key ' . $key . ' wasn\'t deleted.');
            return Command::FAILURE;
        }
    }
}