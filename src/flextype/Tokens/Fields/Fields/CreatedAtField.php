<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onTokensFetchSingleHasResult', static function (): void {

    if (! registry()->get('flextype.settings.entries.tokens.fields.created_at.enabled')) {
        return;
    }

    if (tokens()->registry()->get('fetch.data.created_at') === null) {
        tokens()->registry()->set('fetch.data.created_at', (int) filesystem()->file(tokens()->getFileLocation(tokens()->registry()->get('fetch.id')))->lastModified());
    } else {
        tokens()->registry()->set('fetch.data.created_at', (int) strtotime((string) tokens()->registry()->get('fetch.data.created_at')));
    }
});

emitter()->addListener('onTokensCreate', static function (): void {
    
    if (! registry()->get('flextype.settings.entries.tokens.fields.created_at.enabled')) {
        return;
    }

    if (tokens()->registry()->get('create.data.created_at') !== null) {
        return;
    }

    tokens()->registry()->set('create.data.created_at', date(registry()->get('flextype.settings.date_format'), time()));
});
