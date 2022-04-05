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

emitter()->addListener('onEntriesCreate', static function (): void {
    
    if (! entries()->registry()->get('methods.create.collection.fields.published_by.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.create.params.data.published_by') !== null) {
        return;
    }

    entries()->registry()->set('methods.create.params.data.published_by', '');
});