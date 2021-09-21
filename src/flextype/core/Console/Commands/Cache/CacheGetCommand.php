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

class CacheGetCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:get');
        $this->setDescription('Get key');
        $this->addArgument('key', InputArgument::REQUIRED, 'Key.');
        $this->addArgument('default', InputArgument::OPTIONAL, 'Default.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key     = $input->getArgument('key');
        $default = $input->getArgument('default') ?? null;

        $output->writeln(cache()->get($key, $default));

        return Command::SUCCESS;
    }
}