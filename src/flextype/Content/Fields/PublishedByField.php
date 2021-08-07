<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onContentCreate', static function (): void {
    
    if (registry()->get('flextype.settings.entries.content.fields.published_by.enabled')) {
        return;
    }

    if (content()->registry()->get('create.data.published_by') !== null) {
        return;
    }

    content()->registry()->set('create.data.published_by', '');
});