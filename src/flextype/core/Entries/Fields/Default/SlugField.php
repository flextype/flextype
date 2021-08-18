<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('collectionOptions.fields.slug.enabled')) {
        return;
    }

    if (entries()->registry()->get('fetch.data.slug') !== null) {
        return;
    }

    $parts = explode('/', ltrim(rtrim(entries()->registry()->get('fetch.id'), '/'), '/'));
    entries()->registry()->set('fetch.data.slug', (string) end($parts));
});
