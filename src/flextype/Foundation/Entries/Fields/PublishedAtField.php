<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Filesystem\Filesystem;

if (flextype('registry')->get('flextype.settings.entries.fields.published_at.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function (): void {
        flextype('entries')->storage['fetch_single']['data']['published_at'] = isset(flextype('entries')->storage['fetch_single']['data']['published_at']) ?
                                        (int) strtotime(flextype('entries')->storage['fetch_single']['data']['published_at']) :
                                        (int) Filesystem::getTimestamp(flextype('entries')->getFileLocation(flextype('entries')->storage['fetch_single']['id']));
    });

    flextype('emitter')->addListener('onEntryCreate', static function (): void {
        if (isset(flextype('entries')->storage['create']['data']['published_at'])) {
            flextype('entries')->storage['create']['data']['published_at'] = flextype('entries')->storage['create']['data']['published_at'];
        } else {
            flextype('entries')->storage['create']['data']['published_at'] = date(flextype('registry')->get('flextype.settings.date_format'), time());
        }
    });
}
