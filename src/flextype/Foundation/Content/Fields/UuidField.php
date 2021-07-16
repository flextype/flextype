<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

if (flextype('registry')->get('flextype.settings.entries.content.fields.uuid.enabled')) {
    flextype('emitter')->addListener('onContentCreate', static function (): void {
        if (flextype('content')->registry()->get('create.data.uuid') !== null) {
            return;
        }

        flextype('content')->registry()->set('create.data.uuid', Uuid::uuid4()->toString());
    });
}
