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

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    // Determine is the current field is set and enabled.
    if (! entries()->registry()->get('methods.fetch.collection.fields.routable.enabled')) {
        return;
    }

    // Determine is the current field file path is the same.
    if (! strings(__FILE__)->replace(ROOT_DIR, '')->replaceFirst('/', '')->isEqual(entries()->registry()->get('methods.fetch.collection.fields.routable.path'))) {
        return;
    }

    if (entries()->registry()->get('methods.fetch.result.routable') === null) {
        entries()->registry()->set('methods.fetch.result.routable', true);
    } else {
        entries()->registry()->set('methods.fetch.result.routable', (bool) entries()->registry()->get('methods.fetch.result.routable'));
    }

});

emitter()->addListener('onEntriesCreate', static function (): void {

    // Determine is the current field is set and enabled.
    if (! entries()->registry()->get('methods.create.collection.fields.routable.enabled')) {
        return;
    }

    // Determine is the current field file path is the same.
    if (! strings(__FILE__)->replace(ROOT_DIR, '')->isEqual(entries()->registry()->get('methods.create.collection.fields.routable.path'))) {
        return;
    }

    if (entries()->registry()->get('methods.create.params.data.routable') === null) {
        entries()->registry()->set('methods.create.params.data.routable', true);
    } else {
        entries()->registry()->set('methods.create.params.data.routable', (bool) entries()->registry()->get('methods.create.params.data.routable'));
    }
});
