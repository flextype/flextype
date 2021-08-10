<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

emitter()->addListener('onTokensCreate', static function (): void {

    if (! registry()->get('flextype.settings.entries.tokens.fields.uuid.enabled')) {
        return;
    }

    if (tokens()->registry()->get('create.data.uuid') !== null) {
        return;
    }

    tokens()->registry()->set('create.data.uuid', Uuid::uuid4()->toString());
});
