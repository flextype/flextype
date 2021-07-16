<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.content.fields.created_by.enabled')) {
    flextype('emitter')->addListener('onContentCreate', static function (): void {
        if (flextype('content')->registry()->get('create.data.created_by') !== null) {
            return;
        }

        flextype('content')->registry()->set('create.data.created_by', '');
    });
}
