<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onTokensFetchSingleHasResult', static function (): void {

    if (! registry()->get('flextype.settings.entries.tokens.fields.modified_at.enabled')) {
        return;
    }

    if (tokens()->registry()->get('fetch.data.modified_at') !== null) {
        return;
    }

    tokens()->registry()->set('fetch.data.modified_at', (int) filesystem()->file(tokens()->getFileLocation(tokens()->registry()->get('fetch.id')))->lastModified());
});
