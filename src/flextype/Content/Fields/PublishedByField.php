<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (registry()->get('flextype.settings.storage.content.fields.published_by.enabled')) {
    emitter()->addListener('onContentCreate', static function (): void {
        if (content()->registry()->get('create.data.published_by') !== null) {
            return;
        }

        content()->registry()->set('create.data.published_by', '');
    });
}
