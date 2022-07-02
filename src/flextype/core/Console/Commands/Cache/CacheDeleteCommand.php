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
            $output->write(
                renderToString(
                    div('Cache item with key ' . $key . ' deleted.', 
                        'color-success px-2 py-1')
                )
            );
            return Command::SUCCESS;
        } else {
            $output->write(
                renderToString(
                    div('Cache item with key ' . $key . ' wasn\'t deleted.', 
                        'color-danger px-2 py-1')
                )
            );
            return Command::FAILURE;
        }
    }
}