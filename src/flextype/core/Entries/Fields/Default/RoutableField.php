<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onDefaultFetchSingleHasResult', static function (): void {
    
    if (registry()->get('flextype.settings.entries.collections.default.fields.routable.enabled')) {
        return;
    }

    if (entries()->registry()->get('fetch.data.routable') === null) {
        entries()->registry()->set('fetch.data.routable', true);
    } else {
        entries()->registry()->set('fetch.data.routable', (bool) entries()->registry()->get('fetch.data.routable'));
    }

});

emitter()->addListener('onDefaultCreate', static function (): void {

    if (! registry()->get('flextype.settings.entries.collections.default.fields.routable.enabled')) {
        return;
    }

    if (entries()->registry()->get('create.data.routable') === null) {
        entries()->registry()->set('create.data.routable', true);
    } else {
        entries()->registry()->set('create.data.routable', (bool) entries()->registry()->get('create.data.routable'));
    }
});
