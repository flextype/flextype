<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onTokensCreate', static function (): void {
    
    if (! registry()->get('flextype.settings.entries.tokens.fields.calls.enabled')) {
        return;
    }

    if (tokens()->registry()->get('create.data.calls') !== null) {
        return;
    }

    tokens()->registry()->set('create.data.calls', 0);
});
