<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.content.fields.modified_at.enabled')) {
    flextype('emitter')->addListener('onContentFetchSingleHasResult', static function (): void {
        if (flextype('content')->registry()->get('fetch.data.modified_at') !== null) {
            return;
        }

        flextype('content')->registry()->set('fetch.data.modified_at', (int) filesystem()->file(flextype('content')->getFileLocation(flextype('content')->registry()->get('fetch.id')))->lastModified());
    });
}
