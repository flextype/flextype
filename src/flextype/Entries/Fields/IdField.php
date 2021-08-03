<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (registry()->get('flextype.settings.entries.fields.id.enabled')) {
    
    emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {
   
        if (entries()->registry()->get('fetch.data.id') !== null) {
            return;
        }

        entries()->registry()->set('fetch.data.id', (string) strings(entries()->registry()->get('fetch.id'))->trimSlashes());
    });
}
