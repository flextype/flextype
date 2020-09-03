<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (flextype('registry')->get('flextype.settings.entries.fields.routable.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function () : void {
        flextype('entries')->storage['fetch_single']['data']['routable'] = isset(flextype('entries')->storage['fetch_single']['data']['routable']) ?
                                                        (bool) flextype('entries')->storage['fetch_single']['data']['routable'] :
                                                        true;
    });

    flextype('emitter')->addListener('onEntryCreate', static function () : void {
        if (isset(flextype('entries')->storage['create']['data']['routable']) && is_bool(flextype('entries')->storage['create']['data']['routable'])) {
            flextype('entries')->storage['create']['data']['routable'] = flextype('entries')->storage['create']['data']['routable'];
        } else {
            flextype('entries')->storage['create']['data']['routable'] = true;
        }
    });
}
