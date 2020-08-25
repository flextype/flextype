<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.fields.routable.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function () : void {
        flextype('entries')->entry['routable'] = isset(flextype('entries')->entry['routable']) ?
                                                        (bool) flextype('entries')->entry['routable'] :
                                                        true;
    });

    flextype('emitter')->addListener('onEntryCreate', static function () : void {
        if (isset(flextype('entries')->entry_create_data['routable']) && is_bool(flextype('entries')->entry_create_data['routable'])) {
            flextype('entries')->entry_create_data['routable'] = flextype('entries')->entry_create_data['routable'];
        } else {
            flextype('entries')->entry_create_data['routable'] = true;
        }
    });
}
