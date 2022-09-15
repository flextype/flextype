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

class CacheClearRoutesCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:clear-routes');
        $this->setDescription('Clear cache routes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $routesPath = FLEXTYPE_PATH_TMP . '/routes';

        if (filesystem()->directory($routesPath)->exists()) {
            if (filesystem()->directory($routesPath)->delete()) {
                $output->write(
                    renderToString(
                        div(
                            'Routes were successfully cleared from the cache.',
                            'color-success px-2 py-1'
                        )
                    )
                );
                $result = Command::SUCCESS;
            } else {
                $output->write(
                    renderToString(
                        div(
                            'Routes cache wasn\'t cleared.',
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
                        'Routes cache directory ' . $routesPath . ' doesn\'t exist.',
                        'color-danger px-2 py-1'
                    )
                )
            );
            $result = Command::FAILURE;
        }

        return $result;
    }
}
