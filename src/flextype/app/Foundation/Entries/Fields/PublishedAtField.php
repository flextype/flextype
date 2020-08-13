<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Filesystem\Filesystem;

if ($container->registry->get('flextype.settings.entries.fields.published_at.enabled')) {
    $container->emitter->addListener('onEntryAfterInitialized', function () use ($container) : void {
        $container->entries->entry['published_at'] = isset($container->entries->entry['published_at']) ?
                                        (int) strtotime($container->entries->entry['published_at']) :
                                        (int) Filesystem::getTimestamp($container->entries->getFileLocation($container->entries->entry_id));
    });

    $container->emitter->addListener('onEntryCreate', function () use ($container) : void {
        if (isset($container->entries->entry_create_data['published_at'])) {
            $container->entries->entry_create_data['published_at'] = $container->entries->entry_create_data['published_at'];
        } else {
            $container->entries->entry_create_data['published_at'] = date($container->registry->get('flextype.settings.date_format'), time());
        }
    });
}
