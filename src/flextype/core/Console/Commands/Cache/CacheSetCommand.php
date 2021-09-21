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

class CacheSetCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:set');
        $this->setDescription('Set value');
        $this->addArgument('key', InputArgument::REQUIRED, 'Key.');
        $this->addArgument('value', InputArgument::REQUIRED, 'Value.');
        $this->addArgument('ttl', InputArgument::OPTIONAL, 'Time To Live.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $key   = $input->getArgument('key');
        $value = $input->getArgument('value');
        $ttl   = $input->getArgument('ttl') ?? 300;

        if (cache()->set($key, $value, $ttl)) {
            $io->success('Cache item with key ' . $key . ' create.');
            return Command::SUCCESS;
        } else {
            $io->error('Cache item with key ' . $key . ' wasn\'t created.');
            return Command::FAILURE;
        }
    }
}