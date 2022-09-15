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

use function Glowy\Filesystem\filesystem;
use function Thermage\div;
use function Thermage\renderToString;

class CacheClearDataCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:clear-data');
        $this->setDescription('Clear cache data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $routesData = FLEXTYPE_PATH_TMP . '/data';

        if (filesystem()->directory($routesData)->exists()) {
            if (filesystem()->directory($routesData)->delete()) {
                $output->write(
                    renderToString(
                        div(
                            'Data were successfully cleared from the cache.',
                            'color-success px-2 py-1'
                        )
                    )
                );
                $result = Command::SUCCESS;
            } else {
                $output->write(
                    renderToString(
                        div(
                            'Data cache wasn\'t cleared.',
                            'color-danger px-2 py-1'
                        )
                    )
                );
                $result = Command::FAILURE;
            }
        } else {
            $output->write(
                renderToString(
                    div(
                        'Data cache directory ' . $routesData . ' doesn\'t exist.',
                        'color-danger px-2 py-1'
                    )
                )
            );
            $result = Command::FAILURE;
        }

        return $result;
    }
}
