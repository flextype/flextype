<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (registry()->get('flextype.settings.entries.fields.created_by.enabled')) {
    emitter()->addListener('onEntriesCreate', static function (): void {
        if (entries()->registry()->get('create.data.created_by') !== null) {
            return;
        }

        entries()->registry()->set('create.data.created_by', '');
    });
}
