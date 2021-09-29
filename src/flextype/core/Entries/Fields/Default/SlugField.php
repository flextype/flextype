<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('methods.fetch.collection.fields.slug.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.fetch.result.slug') !== null) {
        return;
    }

    $parts = explode('/', ltrim(rtrim(entries()->registry()->get('methods.fetch.params.id'), '/'), '/'));
    entries()->registry()->set('methods.fetch.result.slug', (string) end($parts));
});
