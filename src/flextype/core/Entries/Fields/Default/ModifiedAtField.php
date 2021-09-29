<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {
    
    if (! entries()->registry()->get('methods.fetch.collection.fields.modified_at.enabled')) {
        return;
    }
    
    if (entries()->registry()->get('methods.fetch.result.modified_at') !== null) {
        return;
    }

    entries()->registry()->set('methods.fetch.result.modified_at', (int) filesystem()->file(entries()->getFileLocation(entries()->registry()->get('methods.fetch.params.id')))->lastModified());
});
