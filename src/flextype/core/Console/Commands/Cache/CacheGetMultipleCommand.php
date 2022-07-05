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
use Symfony\Component\Console\Input\InputOption;
use function Thermage\div;
use function Thermage\renderToString;
use function Flextype\cache;
use function Flextype\collectionFromString;

class CacheGetMultipleCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:get-multiple');
        $this->setDescription('Get multiple items.');
        $this->addArgument('keys', InputArgument::REQUIRED, 'Keys.');
        $this->addArgument('default', InputArgument::OPTIONAL, 'Default.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keys    = $input->getArgument('keys') ? collectionFromString($input->getArgument('keys'), ',') : [];
        $default = $input->getArgument('default') ?? null;

        $data = cache()->getMultiple($keys, $default);

        foreach ($data as $key => $value) {
            $output->write(
                renderToString(
                    div('[b][color=success]Key:[/color][/b] ' . $key . "\n" . '[b][color=success]Value:[/color][/b] ' . $value, 'px-2 border-square border-color-success')
                )
            );
        }
        
        return Command::SUCCESS;
    }
}