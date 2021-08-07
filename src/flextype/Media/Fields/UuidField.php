<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

emitter()->addListener('onMediaCreate', static function (): void {

    if (registry()->get('flextype.settings.entries.media.fields.uuid.enabled')) {
        return;
    }

    if (media()->registry()->get('create.data.uuid') !== null) {
        return;
    }

    media()->registry()->set('create.data.uuid', Uuid::uuid4()->toString());
});
