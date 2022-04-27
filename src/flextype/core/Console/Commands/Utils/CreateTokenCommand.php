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

namespace Flextype\Console\Commands\Utils;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use function Thermage\div;
use function Thermage\renderToString;

class CreateTokenCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('utils:create-token');
        $this->setDescription('Create a new unique token.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token               = generateToken();
        $access_token        = generateToken();
        $hashed_access_token = generateTokenHash($access_token);

        filesystem()->directory(PATH['project'] . '/entries/tokens')->ensureExists();

        if (entries()->create('tokens/' . $token, ['hashed_access_token' => $hashed_access_token])) {
            $output->write(
                renderToString(
                    div('Success: Token [b]' . $token . '[/b] with secret access token [b]' . $access_token . '[/b] created.', 
                        'bg-success px-2 py-1')
                )
            );
            return Command::SUCCESS;
        } else {
            $output->write(
                renderToString(
                    div('Failure: Token wasn\'t created.', 
                        'bg-danger px-2 py-1')
                )
            );
            return Command::FAILURE;
        }
    }
}