<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Ramsey\Uuid\Uuid;

if ($container->registry->get('flextype.settings.entries.fields.uuid.enabled')) {
    $container->emitter->addListener('onEntryCreate', function () use ($container) : void {
        if (isset($container->entries->entry_create_data['uuid'])) {
            $container->entries->entry_create_data['uuid'] = $container->entries->entry_create_data['uuid'];
        } else {
            $container->entries->entry_create_data['uuid'] = Uuid::uuid4()->toString();
        }
    });
}
