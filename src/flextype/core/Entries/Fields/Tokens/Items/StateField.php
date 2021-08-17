<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onTokensItemsCreate', static function (): void {
    
    if (! registry()->get('flextype.settings.entries.tokens_items.fields.state.enabled')) {
        return;
    }

    if (entries()->registry()->get('create.data.state') !== null) {
        return;
    }

    entries()->registry()->set('create.data.state', 'enabled');
});