<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if ($container->registry->get('flextype.settings.entries.fields.visibility.enabled')) {
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    $container->emitter->addListener('onEntryAfterInitialized', function () use ($container, $visibility) : void {
        if (isset($container->entries->entry['visibility']) && in_array($container->entries->entry['visibility'], $visibility)) {
            $container->entries->entry['visibility'] = (string) $visibility[$container->entries->entry['visibility']];
        } else {
            $container->entries->entry['visibility'] = (string) $visibility['visible'];
        }
    });

    $container->emitter->addListener('onEntryCreate', function () use ($container, $visibility) : void {
        if (isset($container->entries->entry_create_data['visibility']) && in_array($container->entries->entry_create_data['visibility'], $visibility)) {
            $container->entries->entry_create_data['visibility'] = $container->entries->entry_create_data['visibility'];
        } else {
            $container->entries->entry_create_data['visibility'] = 'visible';
        }
    });
}
