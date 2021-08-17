<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onDefaultCreate', static function (): void {
    
    if (! registry()->get('flextype.settings.entries.collections.default.fields.published_by.enabled')) {
        return;
    }

    if (entries()->registry()->get('create.data.published_by') !== null) {
        return;
    }

    entries()->registry()->set('create.data.published_by', '');
});