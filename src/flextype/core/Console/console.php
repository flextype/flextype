<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Symfony\Component\Console\Application as ConsoleApplication;
use Flextype\Console\Commands\Entries\EntriesCreateCommand;

$app = new ConsoleApplication();
$app->add(new EntriesCreateCommand());
$app->run();