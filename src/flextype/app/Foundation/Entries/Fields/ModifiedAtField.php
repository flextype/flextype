<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Filesystem\Filesystem;

$flextype->emitter->addListener('onEntryAfterInitialized', function () use ($flextype) : void {
    $flextype->entries->entry['modified_at'] = (int) Filesystem::getTimestamp($flextype->entries->getFileLocation($flextype->entries->entry_path));
});
