<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if ($flextype->container('registry')->get('flextype.settings.entries.fields.created_by.enabled')) {
    $flextype->container('emitter')->addListener('onEntryCreate', static function () use ($flextype) : void {
        if (isset($flextype->container('entries')->entry_create_data['created_by'])) {
            $flextype->container('entries')->entry_create_data['created_by'] = $flextype->container('entries')->entry_create_data['created_by'];
        } else {
            $flextype->container('entries')->entry_create_data['created_by'] = '';
        }
    });
}
