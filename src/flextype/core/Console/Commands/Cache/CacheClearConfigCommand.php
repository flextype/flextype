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
use Symfony\Component\Console\Input\InputOption;
use function Thermage\div;
use function Thermage\renderToString;

class CacheClearConfigCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:clear-config');
        $this->setDescription('Clear cache config.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configPath = PATH['tmp'] . '/config';

        if (filesystem()->directory($configPath)->exists()) {
            if (filesystem()->directory($configPath)->delete()) {
                $output->write(
                    renderToString(
                        div('Success: Config were successfully cleared from the cache.', 
                            'bg-success px-2 py-1')
                    )
                );
                $result = Command::SUCCESS;
            } else {
                $output->write(
                    renderToString(
                        div('Failure: Config cache wasn\'t cleared.', 
                            'bg-danger px-2 py-1')
                    )
                );
                $result = Command::FAILURE;
            }
        } else {
            $output->write(
                renderToString(
                    div('Failure: Config cache directory ' . $configPath . ' doesn\'t exist.', 
                        'bg-danger px-2 py-1')
                )
            );
            $result = Command::FAILURE;
        }

        return $result;
    }
}