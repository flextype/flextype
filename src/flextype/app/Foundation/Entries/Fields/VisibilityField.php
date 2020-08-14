<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if ($flextype->container('registry')->get('flextype.settings.entries.fields.visibility.enabled')) {
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    $flextype->container('emitter')->addListener('onEntryAfterInitialized', static function () use ($flextype, $visibility) : void {
        if (isset($flextype->container('entries')->entry['visibility']) && in_array($flextype->container('entries')->entry['visibility'], $visibility)) {
            $flextype->container('entries')->entry['visibility'] = (string) $visibility[$flextype->container('entries')->entry['visibility']];
        } else {
            $flextype->container('entries')->entry['visibility'] = (string) $visibility['visible'];
        }
    });

    $flextype->container('emitter')->addListener('onEntryCreate', static function () use ($flextype, $visibility) : void {
        if (isset($flextype->container('entries')->entry_create_data['visibility']) && in_array($flextype->container('entries')->entry_create_data['visibility'], $visibility)) {
            $flextype->container('entries')->entry_create_data['visibility'] = $flextype->container('entries')->entry_create_data['visibility'];
        } else {
            $flextype->container('entries')->entry_create_data['visibility'] = 'visible';
        }
    });
}
