<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (registry()->get('flextype.settings.storage.content.fields.slug.enabled')) {
    emitter()->addListener('onContentFetchSingleHasResult', static function (): void {
        if (content()->registry()->get('fetch.data.slug') !== null) {
            return;
        }

        $parts = explode('/', ltrim(rtrim(content()->registry()->get('fetch.id'), '/'), '/'));
        content()->registry()->set('fetch.data.slug', (string) end($parts));
    });
}
