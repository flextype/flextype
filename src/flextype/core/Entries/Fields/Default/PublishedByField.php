<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesCreate', static function (): void {
    
    if (! entries()->registry()->get('collection.options.fields.published_by.enabled')) {
        return;
    }

    if (entries()->registry()->get('create.data.published_by') !== null) {
        return;
    }

    entries()->registry()->set('create.data.published_by', '');
});