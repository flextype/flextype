<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Filesystem\Filesystem;

$flextype->emitter->addListener('onEntryAfterInitialized', function() use ($flextype) {
    $flextype->entries->entry['routable'] = isset($flextype->entries->entry['routable']) ?
                                                    (bool) $flextype->entries->entry['routable'] :
                                                    true;
});

$flextype->emitter->addListener('onEntryCreate', function() use ($flextype) {
    if (isset($flextype->entries->entry_create_data['routable']) && is_bool($flextype->entries->entry_create_data['routable'])) {
        $flextype->entries->entry_create_data['routable'] = $flextype->entries->entry_create_data['routable'];
    } else {
        $flextype->entries->entry_create_data['routable'] = true;
    }
});
