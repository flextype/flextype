<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.fields.id.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->getStorage('fetch_single.data.id') !== null) {
            return;
        }

        flextype('entries')->setStorage('fetch_single.data.id', (string) strings(flextype('entries')->getStorage('fetch_single.id'))->trimSlashes());
    });
}
