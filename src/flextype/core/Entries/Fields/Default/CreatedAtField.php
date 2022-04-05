<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS 
 * and with the full functionality of a traditional CMS!
 * 
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('methods.fetch.collection.fields.created_at.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.fetch.result.created_at') === null) {
        entries()->registry()->set('methods.fetch.result.created_at', (int) filesystem()->file(entries()->getFileLocation(entries()->registry()->get('methods.fetch.params.id')))->lastModified());
    } else {
        entries()->registry()->set('methods.fetch.result.created_at', (int) strtotime((string) entries()->registry()->get('methods.fetch.result.created_at')));
    }
});

emitter()->addListener('onEntriesCreate', static function (): void {

    if (! registry()->get('flextype.settings.entries.collections.default.fields.created_at.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.create.params.data.created_at') !== null) {
        return;
    }

    entries()->registry()->set('methods.create.params.data.created_at', date(registry()->get('flextype.settings.date_format'), time()));
});
