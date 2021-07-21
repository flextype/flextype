<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.modified_at.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->registry()->get('fetch.data.modified_at') !== null) {
            return;
        }

        flextype('entries')->registry()->set('fetch.data.modified_at', (int) filesystem()->file(flextype('entries')->getFileLocation(flextype('entries')->registry()->get('fetch.id')))->lastModified());
    });
}
