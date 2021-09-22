<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console\Commands\Cache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CacheHasCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:has');
        $this->setDescription('Check whether cache item exists.');
        $this->addArgument('key', InputArgument::REQUIRED, 'Key.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $key = $input->getArgument('key');

        if (cache()->has($key)) {
            $io->success('Cache item with key ' . $key . ' exists.');
            return Command::SUCCESS;
        } else {
            $io->error('Cache item with key ' . $key . ' doesn\'t exists.');
            return Command::FAILURE;
        }
    }
}