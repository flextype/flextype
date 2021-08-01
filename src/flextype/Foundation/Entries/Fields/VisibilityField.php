<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (registry()->get('flextype.settings.entries.fields.visibility.enabled')) {
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    emitter()->addListener('onEntriesFetchSingleHasResult', static function () use ($visibility): void {
        if (entries()->registry()->get('fetch.data.visibility') !== null && in_array(entries()->registry()->get('fetch.data.visibility'), $visibility)) {
            entries()->registry()->set('fetch.data.visibility', (string) $visibility[entries()->registry()->get('fetch.data.visibility')]);
        } else {
            entries()->registry()->set('fetch.data.visibility', (string) $visibility['visible']);
        }
    });

    emitter()->addListener('onEntriesCreate', static function () use ($visibility): void {
        if (entries()->registry()->get('create.data.visibility') !== null && in_array(entries()->registry()->get('create.data.visibility'), $visibility)) {
            entries()->registry()->set('create.data.visibility', (string) $visibility[entries()->registry()->get('create.data.visibility')]);
        } else {
            entries()->registry()->set('create.data.visibility', (string) $visibility['visible']);
        }
    });
}
