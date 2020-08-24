<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

if (flextype('registry')->get('flextype.settings.entries.fields.uuid.enabled')) {
    flextype('emitter')->addListener('onEntryCreate', static function () : void {
        if (isset(flextype('entries')->entry_create_data['uuid'])) {
            flextype('entries')->entry_create_data['uuid'] = flextype('entries')->entry_create_data['uuid'];
        } else {
            flextype('entries')->entry_create_data['uuid'] = Uuid::uuid4()->toString();
        }
    });
}
