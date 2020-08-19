<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if ($flextype->container('registry')->get('flextype.settings.entries.fields.id.enabled')) {
    $flextype->container('emitter')->addListener('onEntryAfterInitialized', static function () use ($flextype) : void {
        $flextype->container('entries')->entry['id'] = isset($flextype->container('entries')->entry['id']) ? (string) $flextype->container('entries')->entry['id'] : (string) ltrim(rtrim($flextype->container('entries')->entry_id, '/'), '/');
    });
}
