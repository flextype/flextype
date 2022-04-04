<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('methods.fetch.collection.fields.id.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.fetch.result.id') !== null) {
        return;
    }

    entries()->registry()->set('methods.fetch.result.id', strings(entries()->registry()->get('methods.fetch.params.id'))->trimSlashes()->toString());
});