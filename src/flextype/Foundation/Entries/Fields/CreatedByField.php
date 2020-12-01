<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.created_by.enabled')) {
    flextype('emitter')->addListener('onEntryCreate', static function (): void {
        if (flextype('entries')->getStorage('create.data.created_by') !== null) {
            return;
        }

        flextype('entries')->setStorage('create.data.created_by', '');
    });
}
