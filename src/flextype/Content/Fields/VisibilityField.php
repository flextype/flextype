<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (registry()->get('flextype.settings.storage.content.fields.visibility.enabled')) {
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    emitter()->addListener('onContentFetchSingleHasResult', static function () use ($visibility): void {
        if (content()->registry()->get('fetch.data.visibility') !== null && in_array(content()->registry()->get('fetch.data.visibility'), $visibility)) {
            content()->registry()->set('fetch.data.visibility', (string) $visibility[content()->registry()->get('fetch.data.visibility')]);
        } else {
            content()->registry()->set('fetch.data.visibility', (string) $visibility['visible']);
        }
    });

    emitter()->addListener('onContentCreate', static function () use ($visibility): void {
        if (content()->registry()->get('create.data.visibility') !== null && in_array(content()->registry()->get('create.data.visibility'), $visibility)) {
            content()->registry()->set('create.data.visibility', (string) $visibility[content()->registry()->get('create.data.visibility')]);
        } else {
            content()->registry()->set('create.data.visibility', (string) $visibility['visible']);
        }
    });
}
