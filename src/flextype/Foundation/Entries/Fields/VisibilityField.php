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

    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function () use ($visibility): void {
        if (flextype('entries')->storage()->get('fetch.data.visibility') !== null && in_array(flextype('entries')->storage()->get('fetch.data.visibility'), $visibility)) {
            flextype('entries')->storage()->set('fetch.data.visibility', (string) $visibility[flextype('entries')->storage()->get('fetch.data.visibility')]);
        } else {
            flextype('entries')->storage()->set('fetch.data.visibility', (string) $visibility['visible']);
        }
    });

    flextype('emitter')->addListener('onEntriesCreate', static function () use ($visibility): void {
        if (flextype('entries')->storage()->get('create.data.visibility') !== null && in_array(flextype('entries')->storage()->get('create.data.visibility'), $visibility)) {
            flextype('entries')->storage()->set('create.data.visibility', (string) $visibility[flextype('entries')->storage()->get('create.data.visibility')]);
        } else {
            flextype('entries')->storage()->set('create.data.visibility', (string) $visibility['visible']);
        }
    });
}
