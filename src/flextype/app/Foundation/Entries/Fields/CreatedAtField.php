<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Filesystem\Filesystem;

if ($container->registry->get('flextype.settings.entries.fields.created_at.enabled')) {
    $container->emitter->addListener('onEntryAfterInitialized', function () use ($container) : void {
        $container->entries->entry['created_at'] = isset($container->entries->entry['created_at']) ?
                                        (int) strtotime($container->entries->entry['created_at']) :
                                        (int) Filesystem::getTimestamp($container->entries->getFileLocation($container->entries->entry_id));
    });

    $container->emitter->addListener('onEntryCreate', function () use ($container) : void {
        if (isset($container->entries->entry_create_data['created_at'])) {
            $container->entries->entry_create_data['created_at'] = $container->entries->entry_create_data['created_at'];
        } else {
            $container->entries->entry_create_data['created_at'] = date($container->registry->get('flextype.settings.date_format'), time());
        }
    });
}
