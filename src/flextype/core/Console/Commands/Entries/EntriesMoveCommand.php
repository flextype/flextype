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

namespace Flextype\Console\Commands\Entries;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Flextype\entries;
use function Thermage\div;
use function Thermage\renderToString;

class EntriesMoveCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('entries:move');
        $this->setDescription('Move entry.');
        $this->addArgument('id', InputArgument::REQUIRED, 'Unique identifier of the entry.');
        $this->addArgument('newID', InputArgument::REQUIRED, 'New Unique identifier of the entry');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id    = $input->getArgument('id');
        $newID = $input->getArgument('newID');

        if (! entries()->has($id)) {
            $output->write(
                renderToString(
                    div(
                        'Entry [b]' . $id . '[/b] doesn\'t exists.',
                        'color-danger px-2 py-1'
                    )
                )
            );

            return Command::FAILURE;
        }

        if (entries()->move($id, $newID)) {
            $output->write(
                renderToString(
                    div(
                        'Entry [b]' . $id . '[/b] moved to [b]' . $newID . '[/b]',
                        'color-success px-2 py-1'
                    )
                )
            );

            return Command::SUCCESS;
        }

        $output->write(
            renderToString(
                div(
                    'Entry [b]' . $id . '[/b] wasn\'t moved to [b]' . $newID . '[/b]',
                    'color-danger px-2 py-1'
                )
            )
        );

        return Command::FAILURE;
    }
}
