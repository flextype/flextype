<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.fields.routable.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->storage()->get('fetch.data.routable') === null) {
            flextype('entries')->storage()->set('fetch.data.routable', true);
        } else {
            flextype('entries')->storage()->set('fetch.data.routable', (bool) flextype('entries')->storage()->get('fetch.data.routable'));
        }
    });

    flextype('emitter')->addListener('onEntriesCreate', static function (): void {
        if (flextype('entries')->storage()->get('create.data.routable') === null) {
            flextype('entries')->storage()->set('create.data.routable', true);
        } else {
            flextype('entries')->storage()->set('create.data.routable', (bool) flextype('entries')->storage()->get('create.data.routable'));
        }
    });
}
