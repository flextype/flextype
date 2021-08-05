<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (registry()->get('flextype.settings.storage.content.fields.routable.enabled')) {
    emitter()->addListener('onContentFetchSingleHasResult', static function (): void {
        if (content()->registry()->get('fetch.data.routable') === null) {
            content()->registry()->set('fetch.data.routable', true);
        } else {
            content()->registry()->set('fetch.data.routable', (bool) content()->registry()->get('fetch.data.routable'));
        }
    });

    emitter()->addListener('onContentCreate', static function (): void {
        if (content()->registry()->get('create.data.routable') === null) {
            content()->registry()->set('create.data.routable', true);
        } else {
            content()->registry()->set('create.data.routable', (bool) content()->registry()->get('create.data.routable'));
        }
    });
}
