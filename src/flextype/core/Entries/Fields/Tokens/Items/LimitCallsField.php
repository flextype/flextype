<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesCreate', static function (): void {
    
    if (! entries()->registry()->get('methods.create.collection.fields.limit_calls.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.create.params.data.limit_calls') !== null) {
        return;
    }

    entries()->registry()->set('methods.create.params.data.limit_calls', 0);
});