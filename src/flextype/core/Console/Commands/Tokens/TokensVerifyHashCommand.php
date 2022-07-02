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

namespace Flextype\Console\Commands\Tokens;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Thermage\div;
use function Thermage\renderToString;

class TokensVerifyHashCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('tokens:verify-hash');
        $this->setDescription('Verify token hash.');
        $this->addArgument('token', InputArgument::REQUIRED, 'Token.');
        $this->addArgument('token-hash', InputArgument::REQUIRED, 'Token hash.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        dd($input->getArgument('token-hash'));
        if (verifyTokenHash($input->getArgument('token'), $input->getArgument('token-hash'))) {
            $output->write(
                renderToString(
                    div('Token [b]' . $input->getArgument('token') . ' is verified', 
                        'color-success px-2 py-1')
                )
            );

            return Command::SUCCESS;
        } else {
            $output->write(
                renderToString(
                    div('Token [b]' . $input->getArgument('token') . ' isn\'t verified', 
                        'color-danger px-2 py-1')
                )
            );

            return Command::FAILURE;
        }
    }
}