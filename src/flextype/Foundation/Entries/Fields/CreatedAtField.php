<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Strings\Strings;

if (flextype('registry')->get('flextype.settings.entries.fields.created_at.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function (): void {
        if (flextype('entries')->getStorage('fetch_single.data.created_at') == null) {
            flextype('entries')->setStorage('fetch_single.data.created_at', (int) flextype('filesystem')->file(flextype('entries')->getFileLocation(flextype('entries')->getStorage('fetch_single.id')))->lastModified());
        } else {
            flextype('entries')->setStorage('fetch_single.data.created_at', (int) strtotime(flextype('entries')->getStorage('fetch_single.data.created_at')));
        }
    });

    flextype('emitter')->addListener('onEntryCreate', static function (): void {
        if (flextype('entries')->getStorage('create.data.created_at') == null) {
            flextype('entries')->setStorage('create.data.created_at', date(flextype('registry')->get('flextype.settings.date_format'), time()));
        }
    });
}
