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
use function Glowy\Strings\strings;

class TokensCreateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('tokens:create');
        $this->setDescription('Create a new unique token.');
        $this->addArgument('data', InputArgument::OPTIONAL, 'Data to create for the token entry.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token               = generateToken();
        $access_token        = generateToken();
        $hashed_access_token = generateTokenHash($access_token);

        $data = $input->getArgument('data');

        if ($data) {
            if (strings($data)->isJson()) {
                $dataToSave = serializers()->json()->decode($data);
            } else {
                parse_str($data, $dataToSave);
            }
        } else {
            $dataToSave = [];
        }

        ! entries()->has('tokens') and entries()->create('tokens', ['title' => 'Tokens']);

        if (entries()->create('tokens/' . $token, array_merge($dataToSave, ['hashed_access_token' => $hashed_access_token]))) {
            $output->write(
                renderToString(
                    div('Token [b]' . $token . '[/b] with secret access token [b]' . $access_token . '[/b] created.', 
                        'color-success px-2 py-1')
                )
            );
            return Command::SUCCESS;
        } else {
            $output->write(
                renderToString(
                    div('Token wasn\'t created.', 
                        'color-danger px-2 py-1')
                )
            );
            return Command::FAILURE;
        }
    }
}