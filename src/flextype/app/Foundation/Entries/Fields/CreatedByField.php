<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if ($flextype->registry->get('entries.fields.created_by.settings.enabled')) {
    $flextype->emitter->addListener('onEntryCreate', function () use ($flextype) : void {
        if (isset($flextype->entries->entry_create_data['created_by'])) {
            $flextype->entries->entry_create_data['created_by'] = $flextype->entries->entry_create_data['created_by'];
        } else {
            $flextype->entries->entry_create_data['created_by'] = '';
        }
    });
}
