<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.fields.routable.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function (): void {
        if (flextype('entries')->getStorage('fetch_single.data.routable') == null) {
            flextype('entries')->setStorage('fetch_single.data.routable', true);
        } else {
            flextype('entries')->setStorage('fetch_single.data.routable', (bool) flextype('entries')->getStorage('create.data.routable'));
        }
    });

    flextype('emitter')->addListener('onEntryCreate', static function (): void {
        if (flextype('entries')->getStorage('create.data.routable') != null && is_bool(flextype('entries')->getStorage('create.data.routable'))) {
            flextype('entries')->setStorage('create.data.routable', (bool) flextype('entries')->getStorage('create.data.routable'));
        } else {
            flextype('entries')->setStorage('create.data.routable', true);
        }
    });
}
