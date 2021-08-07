<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onContentFetchSingleHasResult', static function (): void {
    
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    if (! registry()->get('flextype.settings.entries.content.fields.visibility.enabled')) {
        return;
    }

    if (content()->registry()->get('fetch.data.visibility') !== null && in_array(content()->registry()->get('fetch.data.visibility'), $visibility)) {
        content()->registry()->set('fetch.data.visibility', (string) $visibility[content()->registry()->get('fetch.data.visibility')]);
    } else {
        content()->registry()->set('fetch.data.visibility', (string) $visibility['visible']);
    }
});

emitter()->addListener('onContentCreate', static function (): void {

    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    if (! registry()->get('flextype.settings.entries.content.fields.visibility.enabled')) {
        return;
    }
    
    if (content()->registry()->get('create.data.visibility') !== null && in_array(content()->registry()->get('create.data.visibility'), $visibility)) {
        content()->registry()->set('create.data.visibility', (string) $visibility[content()->registry()->get('create.data.visibility')]);
    } else {
        content()->registry()->set('create.data.visibility', (string) $visibility['visible']);
    }
});