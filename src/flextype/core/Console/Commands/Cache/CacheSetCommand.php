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
use Symfony\Component\Console\Input\InputArgument;
use function Thermage\div;
use function Thermage\renderToString;


class CacheSetCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:set');
        $this->setDescription('Set item.');
        $this->addArgument('key', InputArgument::REQUIRED, 'Key.');
        $this->addArgument('value', InputArgument::REQUIRED, 'Value.');
        $this->addArgument('ttl', InputArgument::OPTIONAL, 'Time To Live.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key   = $input->getArgument('key');
        $value = $input->getArgument('value');
        $ttl   = $input->getArgument('ttl') ?? 300;

        if (cache()->set($key, $value, $ttl)) {
            $output->write(
                renderToString(
                    div('Success: Cache item with key ' . $key . ' created.', 
                        'bg-success px-2 py-1')
                )
            );
            return Command::SUCCESS;
        } else {
            $output->write(
                renderToString(
                    div('Failure: Cache item with key ' . $key . ' wasn\'t created.', 
                        'bg-danger px-2 py-1')
                )
            );
            return Command::FAILURE;
        }
    }
}