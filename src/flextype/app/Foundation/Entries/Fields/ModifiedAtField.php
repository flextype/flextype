<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Filesystem\Filesystem;

if ($flextype->container('registry')->get('flextype.settings.entries.fields.modified_at.enabled')) {
    $flextype->container('emitter')->addListener('onEntryAfterInitialized', static function () use ($flextype) : void {
        $flextype->container('entries')->entry['modified_at'] = (int) Filesystem::getTimestamp($flextype->container('entries')->getFileLocation($flextype->container('entries')->entry_id));
    });
}
