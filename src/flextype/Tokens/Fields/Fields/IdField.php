<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onTokensFetchSingleHasResult', static function (): void {

    if (! registry()->get('flextype.settings.entries.tokens.fields.id.enabled')) {
        return;
    }

    if (tokens()->registry()->get('fetch.data.id') !== null) {
        return;
    }

    tokens()->registry()->set('fetch.data.id', strings(tokens()->registry()->get('fetch.id'))->trimSlashes()->toString());
});