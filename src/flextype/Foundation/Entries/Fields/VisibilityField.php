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

    flextype('emitter')->addListener('onEntryAfterInitialized', static function () use ($visibility): void {
        if (flextype('entries')->getStorage('fetch.data.visibility') !== null && in_array(flextype('entries')->getStorage('fetch.data.visibility'), $visibility)) {
            flextype('entries')->setStorage('fetch.data.visibility', (string) $visibility[flextype('entries')->getStorage('fetch.data.visibility')]);
        } else {
            flextype('entries')->setStorage('fetch.data.visibility', (string) $visibility['visible']);
        }
    });

    flextype('emitter')->addListener('onEntryCreate', static function () use ($visibility): void {
        if (flextype('entries')->getStorage('create.data.visibility') !== null && in_array(flextype('entries')->getStorage('create.data.visibility'), $visibility)) {
            flextype('entries')->setStorage('create.data.visibility', (string) $visibility[flextype('entries')->getStorage('create.data.visibility')]);
        } else {
            flextype('entries')->setStorage('create.data.visibility', (string) $visibility['visible']);
        }
    });
}
