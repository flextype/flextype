<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onDefaultFetchSingleHasResult', static function (): void {
    
    if (! registry()->get('flextype.settings.entries.collections.default.fields.modified_at.enabled')) {
        return;
    }
    
    if (entries()->registry()->get('fetch.data.modified_at') !== null) {
        return;
    }

    entries()->registry()->set('fetch.data.modified_at', (int) filesystem()->file(entries()->getFileLocation(entries()->registry()->get('fetch.id')))->lastModified());
});
