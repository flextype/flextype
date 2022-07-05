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
use function Thermage\div;
use function Thermage\renderToString;
use function Flextype\generateToken;
use function Flextype\generateTokenHash;

class TokensGenerateHashCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('tokens:generate-hash');
        $this->setDescription('Generate token hash.');
        $this->addArgument('token', InputArgument::OPTIONAL, 'Token string.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = $input->getArgument('token') ?? generateToken();

        $output->write(
            renderToString(
                div('Hash [b]' . generateTokenHash($token) . '[/b] for token [b]' . $token . '[/b] generated.', 
                    'color-success px-2 py-1')
            )
        );

        return Command::SUCCESS;
    }
}