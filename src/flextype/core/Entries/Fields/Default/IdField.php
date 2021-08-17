<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onDefaultFetchSingleHasResult', static function (): void {
    
    if (! registry()->get('flextype.settings.entries.collections.default.fields.id.enabled')) {
        return;
    }

    if (container()->get('entries')->registry()->get('fetch.data.id') !== null) {
        return;
    }

    container()->get('entries')->registry()->set('fetch.data.id', strings(container()->get('entries')->registry()->get('fetch.id'))->trimSlashes()->toString());
});