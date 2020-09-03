<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.id.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function () : void {
        flextype('entries')->storage['fetch_single']['data']['id'] = isset(flextype('entries')->storage['fetch_single']['data']['id']) ? (string) flextype('entries')->storage['fetch_single']['data']['id'] : (string) ltrim(rtrim(flextype('entries')->storage['fetch_single']['id'], '/'), '/');
    });
}
