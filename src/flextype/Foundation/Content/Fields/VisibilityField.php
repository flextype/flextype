<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.content.fields.visibility.enabled')) {
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    flextype('emitter')->addListener('onContentFetchSingleHasResult', static function () use ($visibility): void {
        if (flextype('content')->registry()->get('fetch.data.visibility') !== null && in_array(flextype('content')->registry()->get('fetch.data.visibility'), $visibility)) {
            flextype('content')->registry()->set('fetch.data.visibility', (string) $visibility[flextype('content')->registry()->get('fetch.data.visibility')]);
        } else {
            flextype('content')->registry()->set('fetch.data.visibility', (string) $visibility['visible']);
        }
    });

    flextype('emitter')->addListener('onContentCreate', static function () use ($visibility): void {
        if (flextype('content')->registry()->get('create.data.visibility') !== null && in_array(flextype('content')->registry()->get('create.data.visibility'), $visibility)) {
            flextype('content')->registry()->set('create.data.visibility', (string) $visibility[flextype('content')->registry()->get('create.data.visibility')]);
        } else {
            flextype('content')->registry()->set('create.data.visibility', (string) $visibility['visible']);
        }
    });
}
