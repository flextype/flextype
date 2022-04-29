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
use Symfony\Component\Console\Input\InputArgument;
use function Thermage\div;
use function Thermage\renderToString;

class CacheSetMultipleCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('cache:set-multiple');
        $this->setDescription('Set multiple items.');
        $this->addArgument('values', InputArgument::REQUIRED, 'Values.');
        $this->addArgument('ttl', InputArgument::OPTIONAL, 'Time To Live.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (strings($input->getArgument('values'))->isJson()) {
            $values = serializers()->json()->decode($input->getArgument('values'));
        } else {
            parse_str($input->getArgument('values'), $values);
        }

        $ttl = $input->getArgument('ttl') ?? 300;

        if (cache()->setMultiple($values, $ttl)) {
            $output->write(
                renderToString(
                    div('Success: Cache items with keys [b]' . implode('[/b], [b]', array_keys($values)) . '[/b] created.', 
                        'bg-success px-2 py-1')
                )
            );
            return Command::SUCCESS;
        } else {
            $output->write(
                renderToString(
                    div('Failure: Cache items with keys [b]' . implode('[/b], [b]', array_keys($values)) . '[/b] wasn\'t created.', 
                        'bg-success px-2 py-1')
                )
            );
            return Command::FAILURE;
        }
    }
}