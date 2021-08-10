<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onContentFetchSingleHasResult', static function (): void {
    
    if (registry()->get('flextype.settings.entries.content.fields.routable.enabled')) {
        return;
    }

    if (content()->registry()->get('fetch.data.routable') === null) {
        content()->registry()->set('fetch.data.routable', true);
    } else {
        content()->registry()->set('fetch.data.routable', (bool) content()->registry()->get('fetch.data.routable'));
    }

});

emitter()->addListener('onContentCreate', static function (): void {

    if (! registry()->get('flextype.settings.entries.content.fields.routable.enabled')) {
        return;
    }

    if (content()->registry()->get('create.data.routable') === null) {
        content()->registry()->set('create.data.routable', true);
    } else {
        content()->registry()->set('create.data.routable', (bool) content()->registry()->get('create.data.routable'));
    }
});
