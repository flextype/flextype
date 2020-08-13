<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if ($container->registry->get('flextype.settings.entries.fields.id.enabled')) {
    $container->emitter->addListener('onEntryAfterInitialized', function () use ($container) : void {
        $container->entries->entry['id'] = isset($container->entries->entry['id']) ? (string) $container->entries->entry['id'] : (string) ltrim(rtrim($container->entries->entry_id, '/'), '/');
    });
}
