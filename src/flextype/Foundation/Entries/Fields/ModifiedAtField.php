<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.modified_at.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function (): void {
        if (flextype('entries')->getStorage('fetch_single.data.modified_at') === null) {
            flextype('entries')->setStorage('fetch_single.data.modified_at', (int) filesystem()->file(flextype('entries')->getFileLocation(flextype('entries')->getStorage('fetch_single.id')))->lastModified());
        }
    });
}
