<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onContentFetchSingleHasResult', static function (): void {

    if (! registry()->get('flextype.settings.entries.content.fields.published_at.enabled')) {
        return;
    }

    if (content()->registry()->get('fetch.data.published_at') === null) {
        content()->registry()->set('fetch.data.published_at', (int) filesystem()->file(content()->getFileLocation(content()->registry()->get('fetch.id')))->lastModified());
    } else {
        content()->registry()->set('fetch.data.published_at', (int) strtotime((string) content()->registry()->get('fetch.data.published_at')));
    }
});

emitter()->addListener('onContentCreate', static function (): void {

    if (! registry()->get('flextype.settings.entries.content.fields.published_at.enabled')) {
        return;
    }

    if (content()->registry()->get('create.data.published_at') !== null) {
        return;
    }

    content()->registry()->set('create.data.published_at', date(registry()->get('flextype.settings.date_format'), time()));
});