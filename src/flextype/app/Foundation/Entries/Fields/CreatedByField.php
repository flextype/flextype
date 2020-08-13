<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if ($container->registry->get('flextype.settings.entries.fields.created_by.enabled')) {
    $container->emitter->addListener('onEntryCreate', function () use ($container) : void {
        if (isset($container->entries->entry_create_data['created_by'])) {
            $container->entries->entry_create_data['created_by'] = $container->entries->entry_create_data['created_by'];
        } else {
            $container->entries->entry_create_data['created_by'] = '';
        }
    });
}
