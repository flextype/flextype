<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if ($container->registry->get('flextype.settings.entries.fields.routable.enabled')) {
    $container->emitter->addListener('onEntryAfterInitialized', function () use ($container) : void {
        $container->entries->entry['routable'] = isset($container->entries->entry['routable']) ?
                                                        (bool) $container->entries->entry['routable'] :
                                                        true;
    });

    $container->emitter->addListener('onEntryCreate', function () use ($container) : void {
        if (isset($container->entries->entry_create_data['routable']) && is_bool($container->entries->entry_create_data['routable'])) {
            $container->entries->entry_create_data['routable'] = $container->entries->entry_create_data['routable'];
        } else {
            $container->entries->entry_create_data['routable'] = true;
        }
    });
}
