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
    
    // Determine is the current field is set and enabled.
    if (! entries()->registry()->get('methods.fetch.collection.fields.visibility.enabled')) {
        return;
    }

    // Determine is the current field file path is the same.
    if (! strings(__FILE__)->replace(ROOT_DIR, '')->isEqual(entries()->registry()->get('methods.fetch.collection.fields.visibility.path'))) {
        return;
    }
    
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];

    if (entries()->registry()->get('methods.fetch.result.visibility') !== null && in_array(entries()->registry()->get('methods.fetch.result.visibility'), $visibility)) {
        entries()->registry()->set('methods.fetch.result.visibility', (string) $visibility[entries()->registry()->get('methods.fetch.result.visibility')]);
    } else {
        entries()->registry()->set('methods.fetch.result.visibility', (string) $visibility['visible']);
    }
});

emitter()->addListener('onEntriesCreate', static function (): void {

    // Determine is the current field is set and enabled.
    if (! entries()->registry()->get('methods.create.collection.fields.visibility.enabled')) {
        return;
    }

    // Determine is the current field file path is the same.
    if (! strings(__FILE__)->replace(ROOT_DIR, '')->isEqual(entries()->registry()->get('methods.create.collection.fields.visibility.path'))) {
        return;
    }
    
    $visibility = [
        'draft' => 'draft',
        'hidden' => 'hidden',
        'visible' => 'visible',
    ];
    
    if (entries()->registry()->get('methods.create.params.data.visibility') !== null && in_array(entries()->registry()->get('methods.create.params.data.visibility'), $visibility)) {
        entries()->registry()->set('methods.create.params.data.visibility', (string) $visibility[entries()->registry()->get('methods.create.params.data.visibility')]);
    } else {
        entries()->registry()->set('methods.create.params.data.visibility', (string) $visibility['visible']);
    }
});