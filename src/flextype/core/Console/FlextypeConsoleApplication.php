<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console;

use Symfony\Component\Console\Application as ConsoleApplication;
use Flextype\Console\Commands\Entries\EntriesCreateCommand;
use Flextype\Console\Commands\Entries\EntriesDeleteCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlextypeConsoleApplication extends ConsoleApplication
{
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        // Add Console Commands
        console()->add(new EntriesCreateCommand());
        console()->add(new EntriesDeleteCommand());
        
        parent::run();
    }
}