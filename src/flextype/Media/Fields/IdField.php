<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onMediaFetchSingleHasResult', static function (): void {

    if (registry()->get('flextype.settings.entries.media.fields.id.enabled')) {
        return;
    }

    if (media()->registry()->get('fetch.data.id') !== null) {
        return;
    }

    media()->registry()->set('fetch.data.id', (string) strings(media()->registry()->get('fetch.id'))->trimSlashes());
});