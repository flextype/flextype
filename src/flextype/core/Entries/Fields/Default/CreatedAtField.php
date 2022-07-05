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

use function Glowy\Strings\strings;
use function Glowy\Filesystem\filesystem;
use function Flextype\emitter;
use function Flextype\entries;
use function Flextype\registry;

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    // Determine is the current field is set and enabled.
    if (! entries()->registry()->get('methods.fetch.collection.fields.created_at.enabled')) {
        return;
    }
    
    // Determine is the current field file path is the same.
    if (! strings(__FILE__)->replace(FLEXTYPE_ROOT_DIR, '')->replaceFirst('/', '')->isEqual(entries()->registry()->get('methods.fetch.collection.fields.created_at.path'))) {
        return;
    }
    
    if (entries()->registry()->get('methods.fetch.result.created_at') === null) {
        entries()->registry()->set('methods.fetch.result.created_at', (int) filesystem()->file(entries()->getFileLocation(entries()->registry()->get('methods.fetch.params.id')))->lastModified());
    } else {
        entries()->registry()->set('methods.fetch.result.created_at', (int) strtotime((string) entries()->registry()->get('methods.fetch.result.created_at')));
    }
});

emitter()->addListener('onEntriesCreate', static function (): void {

    // Determine is the current field is set and enabled.
    if (! registry()->get('methods.fetch.collection.fields.created_at.enabled')) {
        return;
    }

    // Determine is the current field file path is the same.
    if (! strings(__FILE__)->replace(FLEXTYPE_ROOT_DIR, '')->isEqual(entries()->registry()->get('methods.create.collection.fields.created_at.path'))) {
        return;
    }
    
    // Determine is the current field is not null.
    if (entries()->registry()->get('methods.create.params.data.created_at') !== null) {
        return;
    }

    entries()->registry()->set('methods.create.params.data.created_at', date(registry()->get('flextype.settings.date_format'), time()));
});
