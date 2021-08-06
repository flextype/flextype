<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (registry()->get('flextype.settings.entries.media.fields.created_at.enabled')) {
    emitter()->addListener('onMediaFetchSingleHasResult', static function (): void {
        if (media()->registry()->get('fetch.data.created_at') === null) {
            media()->registry()->set('fetch.data.created_at', (int) filesystem()->file(media()->getFileLocation(media()->registry()->get('fetch.id')))->lastModified());
        } else {
            media()->registry()->set('fetch.data.created_at', (int) strtotime((string) media()->registry()->get('fetch.data.created_at')));
        }
    });

    emitter()->addListener('onMediaCreate', static function (): void {
        if (media()->registry()->get('create.data.created_at') !== null) {
            return;
        }

        media()->registry()->set('create.data.created_at', date(registry()->get('flextype.settings.date_format'), time()));
    });
}


