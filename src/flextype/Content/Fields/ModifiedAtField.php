<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (registry()->get('flextype.settings.storage.content.fields.modified_at.enabled')) {
    emitter()->addListener('onContentFetchSingleHasResult', static function (): void {
        if (content()->registry()->get('fetch.data.modified_at') !== null) {
            return;
        }

        content()->registry()->set('fetch.data.modified_at', (int) filesystem()->file(content()->getFileLocation(content()->registry()->get('fetch.id')))->lastModified());
    });
}
