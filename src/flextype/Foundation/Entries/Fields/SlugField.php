<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.fields.slug.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->getStorage('fetch.data.slug') !== null) {
            return;
        }

        $parts = explode('/', ltrim(rtrim(flextype('entries')->getStorage('fetch.id'), '/'), '/'));
        flextype('entries')->setStorage('fetch.data.slug', (string) end($parts));
    });
}
