<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesCreate', static function (): void {
    
    if (! entries()->registry()->get('collectionOptions.fields.state.enabled')) {
        return;
    }

    if (entries()->registry()->get('create.data.state') !== null) {
        return;
    }

    entries()->registry()->set('create.data.state', 'enabled');
});