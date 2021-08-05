<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */


if (registry()->get('flextype.settings.storage.content.fields.id.enabled')) {
    
    emitter()->addListener('onContentFetchSingleHasResult', static function (): void {
   
        if (content()->registry()->get('fetch.data.id') !== null) {
            return;
        }

        content()->registry()->set('fetch.data.id', (string) strings(content()->registry()->get('fetch.id'))->trimSlashes());
    });
}
