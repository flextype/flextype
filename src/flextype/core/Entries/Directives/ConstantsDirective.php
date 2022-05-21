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

use Glowy\Arrays\Arrays as Collection;

// Directive: @const()
emitter()->addListener('onEntriesFetchSingleField', static function (): void {

    if (! registry()->get('flextype.settings.entries.directives.constants.enabled')) {
        return;
    }
    
    $field = entries()->registry()->get('methods.fetch.field');

    if (is_string($field['value'])) {
        $field['value'] = strings($field['value'])
                    ->replace('@const[ROOT_DIR]', ROOT_DIR)
                    ->replace('@const[PATH_PROJECT]', PATH['project'])
                    ->toString();
    }

    entries()->registry()->set('methods.fetch.field.key', $field['key']);
    entries()->registry()->set('methods.fetch.field.value', $field['value']);
});