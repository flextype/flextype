<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesCreate', static function (): void {
    
    if (! entries()->registry()->get('collection.options.fields.total_calls.enabled')) {
        return;
    }

    if (entries()->registry()->get('create.data.total_calls') !== null) {
        return;
    }

    entries()->registry()->set('create.data.total_calls', 0);
});