<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.content.fields.id.enabled')) {
    flextype('emitter')->addListener('onContentFetchSingleHasResult', static function (): void {
        if (flextype('content')->registry()->get('fetch.data.id') !== null) {
            return;
        }

        flextype('content')->registry()->set('fetch.data.id', (string) strings(flextype('content')->registry()->get('fetch.id'))->trimSlashes());
    });
}
