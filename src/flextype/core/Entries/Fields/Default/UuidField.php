<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

emitter()->addListener('onEntriesCreate', static function (): void {

    if (! entries()->registry()->get('methods.create.collection.fields.uuid.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.create.params.data.uuid') !== null) {
        return;
    }

    entries()->registry()->set('methods.create.params.data.uuid', Uuid::uuid4()->toString());
});
