<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (registry()->get('flextype.settings.storage.content.fields.created_by.enabled')) {
    emitter()->addListener('onContentCreate', static function (): void {
        if (content()->registry()->get('create.data.created_by') !== null) {
            return;
        }

        content()->registry()->set('create.data.created_by', '');
    });
}
