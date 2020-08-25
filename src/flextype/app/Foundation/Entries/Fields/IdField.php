<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.id.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function () : void {
        flextype('entries')->entry['id'] = isset(flextype('entries')->entry['id']) ? (string) flextype('entries')->entry['id'] : (string) ltrim(rtrim(flextype('entries')->entry_id, '/'), '/');
    });
}
