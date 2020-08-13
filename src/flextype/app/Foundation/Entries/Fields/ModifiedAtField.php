<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Filesystem\Filesystem;

if ($container->registry->get('flextype.settings.entries.fields.modified_at.enabled')) {
    $container->emitter->addListener('onEntryAfterInitialized', function () use ($container) : void {
        $container->entries->entry['modified_at'] = (int) Filesystem::getTimestamp($container->entries->getFileLocation($container->entries->entry_id));
    });
}
