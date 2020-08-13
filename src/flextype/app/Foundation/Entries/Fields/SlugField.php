<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if ($container->registry->get('flextype.settings.entries.fields.slug.enabled')) {
    $container->emitter->addListener('onEntryAfterInitialized', function () use ($container) : void {
        $parts = explode('/', ltrim(rtrim($container->entries->entry_id, '/'), '/'));
        $container->entries->entry['slug'] = isset($container->entries->entry['slug']) ? (string) $container->entries->entry['slug'] : (string) end($parts);
    });
}
