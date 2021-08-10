<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onTokensCreate', static function (): void {
    
    if (! registry()->get('flextype.settings.entries.tokens.fields.state.enabled')) {
        return;
    }

    if (tokens()->registry()->get('create.data.state') !== null) {
        return;
    }

    tokens()->registry()->set('create.data.state', 'enabled');
});