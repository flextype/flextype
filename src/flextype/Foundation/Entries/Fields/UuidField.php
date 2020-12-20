<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

if (flextype('registry')->get('flextype.settings.entries.fields.uuid.enabled')) {
    flextype('emitter')->addListener('onEntriesCreate', static function (): void {
        if (flextype('entries')->getStorage('create.data.uuid') !== null) {
            return;
        }

        flextype('entries')->setStorage('create.data.uuid', Uuid::uuid4()->toString());
    });
}
