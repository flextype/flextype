<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onDefaultCreate', static function (): void {
   
    if (! registry()->get('flextype.settings.entries.collections.default.fields.created_by.enabled')) {
        return;
    }
    
    if (entries()->registry()->get('create.data.created_by') !== null) {
        return;
    }

    entries()->registry()->set('create.data.created_by', '');
});

