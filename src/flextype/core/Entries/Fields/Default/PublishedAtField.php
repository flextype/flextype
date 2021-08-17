<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onDefaultFetchSingleHasResult', static function (): void {

    if (! registry()->get('flextype.settings.entries.collections.default.fields.published_at.enabled')) {
        return;
    }

    if (entries()->registry()->get('fetch.data.published_at') === null) {
        entries()->registry()->set('fetch.data.published_at', (int) filesystem()->file(entries()->getFileLocation(entries()->registry()->get('fetch.id')))->lastModified());
    } else {
        entries()->registry()->set('fetch.data.published_at', (int) strtotime((string) entries()->registry()->get('fetch.data.published_at')));
    }
});

emitter()->addListener('onDefaultCreate', static function (): void {

    if (! registry()->get('flextype.settings.entries.collections.default.fields.published_at.enabled')) {
        return;
    }

    if (entries()->registry()->get('create.data.published_at') !== null) {
        return;
    }

    entries()->registry()->set('create.data.published_at', date(registry()->get('flextype.settings.date_format'), time()));
});