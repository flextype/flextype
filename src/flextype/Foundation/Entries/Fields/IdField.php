<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Strings\Strings;

if (flextype('registry')->get('flextype.settings.entries.fields.id.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function (): void {
        if (flextype('entries')->getStorage('fetch_single.data.id') == null) {
            flextype('entries')->setStorage('fetch_single.data.id', (string) Strings::create(flextype('entries')->getStorage('fetch_single.id'))->trimSlashes());
        }
    });
}
