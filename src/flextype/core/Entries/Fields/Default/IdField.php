<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('collection.options.fields.id.enabled')) {
        return;
    }

    if (entries()->registry()->get('fetch.data.id') !== null) {
        return;
    }

    entries()->registry()->set('fetch.data.id', strings(entries()->registry()->get('fetch.id'))->trimSlashes()->toString());
});