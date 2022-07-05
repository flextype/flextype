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
use function Flextype\emitter;
use function Flextype\entries;

emitter()->addListener('onEntriesCreate', static function (): void {
    
    // Determine is the current field is set and enabled.
    if (! entries()->registry()->get('methods.create.collection.fields.limit_calls.enabled')) {
        return;
    }
    
    // Determine is the current field file path is the same.
    if (! strings(__FILE__)->replace(FLEXTYPE_ROOT_DIR, '')->replaceFirst('/', '')->isEqual(entries()->registry()->get('methods.create.collection.fields.limit_calls.path'))) {
        return;
    }

    // Determine is the current field is not null.
    if (entries()->registry()->get('methods.create.params.data.limit_calls') !== null) {
        return;
    }

    entries()->registry()->set('methods.create.params.data.limit_calls', 0);
});