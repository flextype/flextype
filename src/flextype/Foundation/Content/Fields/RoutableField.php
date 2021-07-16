<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.content.fields.routable.enabled')) {
    flextype('emitter')->addListener('onContentFetchSingleHasResult', static function (): void {
        if (flextype('content')->registry()->get('fetch.data.routable') === null) {
            flextype('content')->registry()->set('fetch.data.routable', true);
        } else {
            flextype('content')->registry()->set('fetch.data.routable', (bool) flextype('content')->registry()->get('fetch.data.routable'));
        }
    });

    flextype('emitter')->addListener('onContentCreate', static function (): void {
        if (flextype('content')->registry()->get('create.data.routable') === null) {
            flextype('content')->registry()->set('create.data.routable', true);
        } else {
            flextype('content')->registry()->set('create.data.routable', (bool) flextype('content')->registry()->get('create.data.routable'));
        }
    });
}
