<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

$flextype->emitter->addListener('onEntryCreate', function () use ($flextype) {
    if (isset($flextype->entries->entry_create_data['created_by'])) {
        $flextype->entries->entry_create_data['created_by'] = $flextype->entries->entry_create_data['created_by'];
    } else {
        $flextype->entries->entry_create_data['created_by'] = '';
    }
});
