<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

if (registry()->get('flextype.settings.storage.content.fields.uuid.enabled')) {
    emitter()->addListener('onContentCreate', static function (): void {
        if (content()->registry()->get('create.data.uuid') !== null) {
            return;
        }

        content()->registry()->set('create.data.uuid', Uuid::uuid4()->toString());
    });
}
