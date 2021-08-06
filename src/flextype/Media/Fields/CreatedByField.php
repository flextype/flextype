<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (registry()->get('flextype.settings.entries.media.fields.created_by.enabled')) {
    emitter()->addListener('onMediaCreate', static function (): void {
        if (media()->registry()->get('create.data.created_by') !== null) {
            return;
        }

        media()->registry()->set('create.data.created_by', '');
    });
}
