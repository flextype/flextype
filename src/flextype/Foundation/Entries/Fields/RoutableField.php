<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.fields.routable.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->getStorage('fetch.data.routable') === null) {
            flextype('entries')->setStorage('fetch.data.routable', true);
        } else {
            flextype('entries')->setStorage('fetch.data.routable', (bool) flextype('entries')->getStorage('fetch.data.routable'));
        }
    });

    flextype('emitter')->addListener('onEntriesCreate', static function (): void {
        if (flextype('entries')->getStorage('create.data.routable') === null) {
            flextype('entries')->setStorage('create.data.routable', true);
        } else {
            flextype('entries')->setStorage('create.data.routable', (bool) flextype('entries')->getStorage('create.data.routable'));
        }
    });
}
