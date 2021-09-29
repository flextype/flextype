<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('methods.fetch.collection.fields.routable.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.fetch.result.routable') === null) {
        entries()->registry()->set('methods.fetch.result.routable', true);
    } else {
        entries()->registry()->set('methods.fetch.result.routable', (bool) entries()->registry()->get('methods.fetch.result.routable'));
    }

});

emitter()->addListener('onEntriesCreate', static function (): void {

    if (! entries()->registry()->get('methods.create.collection.fields.routable.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.create.params.data.routable') === null) {
        entries()->registry()->set('methods.create.params.data.routable', true);
    } else {
        entries()->registry()->set('methods.create.params.data.routable', (bool) entries()->registry()->get('methods.create.params.data.routable'));
    }
});
