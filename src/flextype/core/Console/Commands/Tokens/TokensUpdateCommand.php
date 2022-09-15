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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Flextype\entries;
use function Flextype\serializers;
use function Glowy\Strings\strings;
use function parse_str;
use function Thermage\div;
use function Thermage\renderToString;

class TokensUpdateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('tokens:update');
        $this->setDescription('Update tokens entry.');
        $this->addArgument('id', InputArgument::REQUIRED, 'Unique identifier of the token entry.');
        $this->addArgument('data', InputArgument::OPTIONAL, 'Data to update for the token entry.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id   = $input->getArgument('id');
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

        if (! entries()->has($id)) {
            $output->write(
                renderToString(
                    div(
                        'Token entry [b]' . $id . '[/b] doesn\'t exists.',
                        'color-danger px-2 py-1'
                    )
                )
            );

            return Command::FAILURE;
        }

        if (entries()->update($id, $dataToSave)) {
            $output->write(
                renderToString(
                    div(
                        'Token entry [b]' . $id . '[/b] updated.',
                        'color-success px-2 py-1'
                    )
                )
            );

            return Command::SUCCESS;
        }

        $output->write(
            renderToString(
                div(
                    'Token entry [b]' . $id . '[/b] wasn\'t updated.',
                    'color-danger px-2 py-1'
                )
            )
        );

        return Command::FAILURE;
    }
}
