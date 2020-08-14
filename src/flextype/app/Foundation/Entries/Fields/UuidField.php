<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

if ($flextype->container('registry')->get('flextype.settings.entries.fields.uuid.enabled')) {
    $flextype->container('emitter')->addListener('onEntryCreate', static function () use ($flextype) : void {
        if (isset($flextype->container('entries')->entry_create_data['uuid'])) {
            $flextype->container('entries')->entry_create_data['uuid'] = $flextype->container('entries')->entry_create_data['uuid'];
        } else {
            $flextype->container('entries')->entry_create_data['uuid'] = Uuid::uuid4()->toString();
        }
    });
}
