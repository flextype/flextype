<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.fields.created_at.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->registry()->get('fetch.data.created_at') === null) {
            flextype('entries')->registry()->set('fetch.data.created_at', (int) filesystem()->file(flextype('entries')->getFileLocation(flextype('entries')->registry()->get('fetch.id')))->lastModified());
        } else {
            flextype('entries')->registry()->set('fetch.data.created_at', (int) strtotime((string) flextype('entries')->registry()->get('fetch.data.created_at')));
        }
    });

    flextype('emitter')->addListener('onEntriesCreate', static function (): void {
        if (flextype('entries')->registry()->get('create.data.created_at') !== null) {
            return;
        }

        flextype('entries')->registry()->set('create.data.created_at', date(flextype('registry')->get('flextype.settings.date_format'), time()));
    });
}
