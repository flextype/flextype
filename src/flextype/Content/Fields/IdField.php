<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onContentFetchSingleHasResult', static function (): void {
    
    if (! registry()->get('flextype.settings.entries.content.fields.id.enabled')) {
        return;
    }

    if (content()->registry()->get('fetch.data.id') !== null) {
        return;
    }

    content()->registry()->set('fetch.data.id', strings(content()->registry()->get('fetch.id'))->trimSlashes()->toString());
});