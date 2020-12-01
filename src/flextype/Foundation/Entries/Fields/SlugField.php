<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Strings\Strings;

if (flextype('registry')->get('flextype.settings.entries.fields.slug.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function (): void {
        if (flextype('entries')->getStorage('fetch.data.slug') !== null) {
            return;
        }

        $parts = Strings::create(flextype('entries')->getStorage('fetch.id'))->trimSlashes()->segments();
        flextype('entries')->setStorage('fetch.data.slug', (string) end($parts));
    });
}
