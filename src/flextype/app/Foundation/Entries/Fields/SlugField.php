<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if ($flextype->container('registry')->get('flextype.settings.entries.fields.slug.enabled')) {
    $flextype->container('emitter')->addListener('onEntryAfterInitialized', static function () use ($flextype) : void {
        $parts                                         = explode('/', ltrim(rtrim($flextype->container('entries')->entry_id, '/'), '/'));
        $flextype->container('entries')->entry['slug'] = isset($flextype->container('entries')->entry['slug']) ? (string) $flextype->container('entries')->entry['slug'] : (string) end($parts);
    });
}
