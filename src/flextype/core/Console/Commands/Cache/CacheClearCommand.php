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
use Symfony\Component\Console\Input\InputOption;
use function Thermage\div;
use function Thermage\renderToString;

class CacheClearCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:clear');
        $this->setDescription('Clear cache.');
        $this->addOption('data', null, InputOption::VALUE_NONE, 'Set this flag to clear data from cache.');
        $this->addOption('config', null, InputOption::VALUE_NONE, 'Set this flag to clear config from cache.');
        $this->addOption('routes', null, InputOption::VALUE_NONE, 'Set this flag to clear routes from cache.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = Command::SUCCESS;

        if ($input->getOption('data')) {
            if (filesystem()->directory(PATH['tmp'] . '/data')->exists()) {
                if (filesystem()->directory(PATH['tmp'] . '/data')->delete()) {
                    $output->write(
                        renderToString(
                            div('Success: Data were successfully cleared from the cache.', 
                                'bg-success px-2 py-1')
                        )
                    );
                    $result = Command::SUCCESS;
                } else {
                    $output->write(
                        renderToString(
                            div('Failure: Data cache wasn\'t cleared.', 
                                'bg-danger px-2 py-1')
                        )
                    );
                    $result = Command::FAILURE;
                }
            } else {
                $output->write(
                    renderToString(
                        div('Failure: Data cache directory ' . PATH['tmp'] . '/data' . ' doesn\'t exist.', 
                            'bg-danger px-2 py-1')
                    )
                );
                $result = Command::FAILURE;
            }
        }

        if ($input->getOption('config')) {
            if (filesystem()->directory(PATH['tmp'] . '/config')->exists()) {
                if (filesystem()->directory(PATH['tmp'] . '/config')->delete()) {
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
                        div('Failure: Config cache directory ' . PATH['tmp'] . '/config' . ' doesn\'t exist.', 
                            'bg-danger px-2 py-1')
                    )
                );
                $result = Command::FAILURE;
            }
        }

        if ($input->getOption('routes')) {
            if (filesystem()->directory(PATH['tmp'] . '/routes')->exists()) {
                if (filesystem()->directory(PATH['tmp'] . '/routes')->delete()) {
                    $output->write(
                        renderToString(
                            div('Success: Routes were successfully cleared from the cache.', 
                                'bg-success px-2 py-1')
                        )
                    );
                    $result = Command::SUCCESS;
                } else {
                    $output->write(
                        renderToString(
                            div('Failure: Routes cache wasn\'t cleared.', 
                                'bg-danger px-2 py-1')
                        )
                    );
                    $result = Command::FAILURE;
                }
            } else {
                $output->write(
                    renderToString(
                        div('Failure: Routes cache directory ' . PATH['tmp'] . '/routes' . ' doesn\'t exist.', 
                            'bg-danger px-2 py-1')
                    )
                );
                $result = Command::FAILURE;
            }
        }

        if (($input->getOption('data') == false && 
             $input->getOption('config') == false &&
             $input->getOption('routes') == false)) {
            if (filesystem()->directory(PATH['tmp'])->exists()) {
                if (filesystem()->directory(PATH['tmp'])->delete()) {
                    $output->write(
                        renderToString(
                            div('Success: All items were successfully cleared from the cache.', 
                                'bg-success px-2 py-1')
                        )
                    );
                    $result = Command::SUCCESS;
                } else {
                    $output->write(
                        renderToString(
                            div('Failure: Cache wasn\'t cleared.', 
                                'bg-danger px-2 py-1')
                        )
                    );
                    $result = Command::FAILURE;
                }
            } else {
                $output->write(
                    renderToString(
                        div('Failure: Cache directory ' . PATH['tmp'] . ' doesn\'t exist.', 
                            'bg-danger px-2 py-1')
                    )
                );
                $result = Command::FAILURE;
            }
        }

        return $result;
    }
}