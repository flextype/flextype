<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.visibility.enabled')) {
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    flextype('emitter')->addListener('onEntryAfterInitialized', static function () use ($visibility) : void {
        if (isset(flextype('entries')->entry['visibility']) && in_array(flextype('entries')->entry['visibility'], $visibility)) {
            flextype('entries')->entry['visibility'] = (string) $visibility[flextype('entries')->entry['visibility']];
        } else {
            flextype('entries')->entry['visibility'] = (string) $visibility['visible'];
        }
    });

    flextype('emitter')->addListener('onEntryCreate', static function () use ($visibility) : void {
        if (isset(flextype('entries')->entry_create_data['visibility']) && in_array(flextype('entries')->entry_create_data['visibility'], $visibility)) {
            flextype('entries')->entry_create_data['visibility'] = flextype('entries')->entry_create_data['visibility'];
        } else {
            flextype('entries')->entry_create_data['visibility'] = 'visible';
        }
    });
}
