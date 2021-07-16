<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.content.fields.slug.enabled')) {
    flextype('emitter')->addListener('onContentFetchSingleHasResult', static function (): void {
        if (flextype('content')->registry()->get('fetch.data.slug') !== null) {
            return;
        }

        $parts = explode('/', ltrim(rtrim(flextype('content')->registry()->get('fetch.id'), '/'), '/'));
        flextype('content')->registry()->set('fetch.data.slug', (string) end($parts));
    });
}
