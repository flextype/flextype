<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onDefaultFetchSingleHasResult', static function (): void {
    
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    if (! registry()->get('flextype.settings.entries.collections.default.fields.visibility.enabled')) {
        return;
    }

    if (entries()->registry()->get('fetch.data.visibility') !== null && in_array(entries()->registry()->get('fetch.data.visibility'), $visibility)) {
        entries()->registry()->set('fetch.data.visibility', (string) $visibility[entries()->registry()->get('fetch.data.visibility')]);
    } else {
        entries()->registry()->set('fetch.data.visibility', (string) $visibility['visible']);
    }
});

emitter()->addListener('onDefaultCreate', static function (): void {

    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    if (! registry()->get('flextype.settings.entries.collections.default.fields.visibility.enabled')) {
        return;
    }
    
    if (entries()->registry()->get('create.data.visibility') !== null && in_array(entries()->registry()->get('create.data.visibility'), $visibility)) {
        entries()->registry()->set('create.data.visibility', (string) $visibility[entries()->registry()->get('create.data.visibility')]);
    } else {
        entries()->registry()->set('create.data.visibility', (string) $visibility['visible']);
    }
});