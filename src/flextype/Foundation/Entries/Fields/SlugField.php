<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Filesystem\Filesystem;

$flextype->emitter->addListener('onEntryAfterInitialized', function() use ($flextype) {
    $flextype->entries->entry['slug'] = isset($flextype->entries->entry['slug']) ? (string) $flextype->entries->entry['slug'] : (string) ltrim(rtrim($flextype->entries->entry_path, '/'), '/');
});
