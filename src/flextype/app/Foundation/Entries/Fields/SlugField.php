<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.slug.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function () : void {
        $parts                                         = explode('/', ltrim(rtrim(flextype('entries')->entry_id, '/'), '/'));
        flextype('entries')->entry['slug'] = isset(flextype('entries')->entry['slug']) ? (string) flextype('entries')->entry['slug'] : (string) end($parts);
    });
}
