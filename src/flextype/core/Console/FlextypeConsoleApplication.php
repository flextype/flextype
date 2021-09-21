<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Console;

use Symfony\Component\Console\Application as ConsoleApplication;
use Flextype\Console\Commands\Entries\EntriesCreateCommand;
use Flextype\Console\Commands\Entries\EntriesFetchCommand;
use Flextype\Console\Commands\Entries\EntriesUpdateCommand;
use Flextype\Console\Commands\Entries\EntriesDeleteCommand;
use Flextype\Console\Commands\Entries\EntriesCopyCommand;
use Flextype\Console\Commands\Entries\EntriesMoveCommand;
use Flextype\Console\Commands\Entries\EntriesHasCommand;
use Flextype\Console\Commands\Cache\CacheDeleteCommand;
use Flextype\Console\Commands\Cache\CacheSetCommand;
use Flextype\Console\Commands\Cache\CacheGetCommand;
use Flextype\Console\Commands\Cache\CacheGetMultipleCommand;
use Flextype\Console\Commands\Cache\CacheSetMultipleCommand;
use Flextype\Console\Commands\Cache\CacheClearCommand;
use Flextype\Console\Commands\Cache\CacheHasCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlextypeConsoleApplication extends ConsoleApplication
{
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        // Add Console Commands
        console()->add(new EntriesCreateCommand());
        console()->add(new EntriesDeleteCommand());
        console()->add(new EntriesUpdateCommand());
        console()->add(new EntriesCopyCommand());
        console()->add(new EntriesMoveCommand());
        console()->add(new EntriesHasCommand());
        console()->add(new EntriesFetchCommand());
        console()->add(new CacheSetCommand());
        console()->add(new CacheGetCommand());
        console()->add(new CacheGetMultipleCommand());
        console()->add(new CacheSetMultipleCommand());
        console()->add(new CacheDeleteCommand());
        console()->add(new CacheClearCommand());
        console()->add(new CacheHasCommand());
        
        parent::run();
    }
}