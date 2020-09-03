<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.visibility.enabled')) {
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    flextype('emitter')->addListener('onEntryAfterInitialized', static function () use ($visibility) : void {
        if (isset(flextype('entries')->storage['fetch_single']['data']['visibility']) && in_array(flextype('entries')->storage['fetch_single']['data']['visibility'], $visibility)) {
            flextype('entries')->storage['fetch_single']['data']['visibility'] = (string) $visibility[flextype('entries')->storage['fetch_single']['data']['visibility']];
        } else {
            flextype('entries')->storage['fetch_single']['data']['visibility'] = (string) $visibility['visible'];
        }
    });

    flextype('emitter')->addListener('onEntryCreate', static function () use ($visibility) : void {
        if (isset(flextype('entries')->storage['create']['data']['visibility']) && in_array(flextype('entries')->storage['create']['data']['visibility'], $visibility)) {
            flextype('entries')->storage['create']['data']['visibility'] = flextype('entries')->storage['create']['data']['visibility'];
        } else {
            flextype('entries')->storage['create']['data']['visibility'] = 'visible';
        }
    });
}
