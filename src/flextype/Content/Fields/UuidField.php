<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

emitter()->addListener('onContentCreate', static function (): void {

    if (! registry()->get('flextype.settings.entries.content.fields.uuid.enabled')) {
        return;
    }

    if (content()->registry()->get('create.data.uuid') !== null) {
        return;
    }

    content()->registry()->set('create.data.uuid', Uuid::uuid4()->toString());
});
