<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {
    
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    if (! entries()->registry()->get('methods.fetch.collection.fields.visibility.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.fetch.result.visibility') !== null && in_array(entries()->registry()->get('methods.fetch.result.visibility'), $visibility)) {
        entries()->registry()->set('methods.fetch.result.visibility', (string) $visibility[entries()->registry()->get('methods.fetch.result.visibility')]);
    } else {
        entries()->registry()->set('methods.fetch.result.visibility', (string) $visibility['visible']);
    }
});

emitter()->addListener('onEntriesCreate', static function (): void {

    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    if (! entries()->registry()->get('methods.create.collection.fields.visibility.enabled')) {
        return;
    }
    
    if (entries()->registry()->get('methods.create.params.data.visibility') !== null && in_array(entries()->registry()->get('methods.create.params.data.visibility'), $visibility)) {
        entries()->registry()->set('methods.create.params.data.visibility', (string) $visibility[entries()->registry()->get('methods.create.params.data.visibility')]);
    } else {
        entries()->registry()->set('methods.create.params.data.visibility', (string) $visibility['visible']);
    }
});