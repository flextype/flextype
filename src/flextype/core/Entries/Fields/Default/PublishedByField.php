<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesCreate', static function (): void {
    
    if (! entries()->registry()->get('methods.create.collection.fields.published_by.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.create.params.data.published_by') !== null) {
        return;
    }

    entries()->registry()->set('methods.create.params.data.published_by', '');
});