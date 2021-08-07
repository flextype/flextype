<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onContentFetchSingleHasResult', static function (): void {

    if (! registry()->get('flextype.settings.entries.content.fields.created_at.enabled')) {
        return;
    }

    if (content()->registry()->get('fetch.data.created_at') === null) {
        content()->registry()->set('fetch.data.created_at', (int) filesystem()->file(content()->getFileLocation(content()->registry()->get('fetch.id')))->lastModified());
    } else {
        content()->registry()->set('fetch.data.created_at', (int) strtotime((string) content()->registry()->get('fetch.data.created_at')));
    }
});

emitter()->addListener('onContentCreate', static function (): void {

    if (registry()->get('flextype.settings.entries.content.fields.created_at.enabled')) {
        return;
    }

    if (content()->registry()->get('create.data.created_at') !== null) {
        return;
    }

    content()->registry()->set('create.data.created_at', date(registry()->get('flextype.settings.date_format'), time()));
});
