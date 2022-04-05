<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS 
 * and with the full functionality of a traditional CMS!
 * 
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
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
        $this->setDescription('Get item.');
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