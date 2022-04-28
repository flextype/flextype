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
use Flextype\Console\Commands\Cache\CacheDeleteMultipleCommand;
use Flextype\Console\Commands\Cache\CacheClearCommand;
use Flextype\Console\Commands\Cache\CacheClearRoutesCommand;
use Flextype\Console\Commands\Cache\CacheClearConfigCommand;
use Flextype\Console\Commands\Cache\CacheClearDataCommand;
use Flextype\Console\Commands\Cache\CacheHasCommand;
use Flextype\Console\Commands\Utils\GenerateTokenCommand;
use Flextype\Console\Commands\Utils\GenerateTokenHashCommand;
use Flextype\Console\Commands\Utils\VerifyTokenHashCommand;
use Flextype\Console\Commands\Utils\CreateTokenCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlextypeConsole extends ConsoleApplication
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
        console()->add(new CacheDeleteMultipleCommand());
        console()->add(new CacheDeleteCommand());
        console()->add(new CacheClearCommand());
        console()->add(new CacheClearRoutesCommand());
        console()->add(new CacheClearConfigCommand());
        console()->add(new CacheClearDataCommand());
        console()->add(new CacheHasCommand());
        console()->add(new GenerateTokenCommand());
        console()->add(new GenerateTokenHashCommand());
        console()->add(new VerifyTokenHashCommand());
        console()->add(new CreateTokenCommand());

        parent::run();
    }
}