<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.slug.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function () : void {

        $parts                                         = explode('/', ltrim(rtrim(flextype('entries')->storage['fetch_single']['id'], '/'), '/'));
        flextype('entries')->storage['fetch_single']['data']['slug'] = isset(flextype('entries')->storage['fetch_single']['data']['slug']) ? (string) flextype('entries')->storage['fetch_single']['data']['slug']: (string) end($parts);
    });
}
