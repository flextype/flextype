<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('collection.options.fields.routable.enabled')) {
        return;
    }

    if (entries()->registry()->get('fetch.result.routable') === null) {
        entries()->registry()->set('fetch.result.routable', true);
    } else {
        entries()->registry()->set('fetch.result.routable', (bool) entries()->registry()->get('fetch.result.routable'));
    }

});

emitter()->addListener('onEntriesCreate', static function (): void {

    if (! entries()->registry()->get('collection.options.fields.routable.enabled')) {
        return;
    }

    if (entries()->registry()->get('create.data.routable') === null) {
        entries()->registry()->set('create.data.routable', true);
    } else {
        entries()->registry()->set('create.data.routable', (bool) entries()->registry()->get('create.data.routable'));
    }
});
