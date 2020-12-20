<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.published_at.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->getStorage('fetch.data.published_at') === null) {
            flextype('entries')->setStorage('fetch.data.published_at', (int) filesystem()->file(flextype('entries')->getFileLocation(flextype('entries')->getStorage('fetch.id')))->lastModified());
        } else {
            flextype('entries')->setStorage('fetch.data.published_at', (int) strtotime((string) flextype('entries')->getStorage('fetch.data.published_at')));
        }
    });

    flextype('emitter')->addListener('onEntriesCreate', static function (): void {
        if (flextype('entries')->getStorage('create.data.published_at') !== null) {
            return;
        }

        flextype('entries')->setStorage('create.data.published_at', date(flextype('registry')->get('flextype.settings.date_format'), time()));
    });
}
