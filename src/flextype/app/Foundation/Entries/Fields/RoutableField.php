<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if ($flextype->container('registry')->get('flextype.settings.entries.fields.routable.enabled')) {
    $flextype->container('emitter')->addListener('onEntryAfterInitialized', static function () use ($flextype) : void {
        $flextype->container('entries')->entry['routable'] = isset($flextype->container('entries')->entry['routable']) ?
                                                        (bool) $flextype->container('entries')->entry['routable'] :
                                                        true;
    });

    $flextype->container('emitter')->addListener('onEntryCreate', static function () use ($flextype) : void {
        if (isset($flextype->container('entries')->entry_create_data['routable']) && is_bool($flextype->container('entries')->entry_create_data['routable'])) {
            $flextype->container('entries')->entry_create_data['routable'] = $flextype->container('entries')->entry_create_data['routable'];
        } else {
            $flextype->container('entries')->entry_create_data['routable'] = true;
        }
    });
}
