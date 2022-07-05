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

use function Flextype\registry;
use function Flextype\emitter;
use function Flextype\entries;
use function Flextype\collection;

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! registry()->get('flextype.settings.entries.macros.entries.enabled')) {
        return;
    }
    
    if (entries()->registry()->has('methods.fetch.result.macros.entries.fetch') &&
        registry()->get('flextype.settings.entries.macros.entries.fetch.enabled') === true) {

        // Backup current entry data
        $original = entries()->registry()->get('methods.fetch');
        $data = [];

        foreach (entries()->registry()->get('methods.fetch.result.macros.entries.fetch') as $key => $value) {
            if (isset($value['type'])) {
                $type = $value['type'];
                if ($type == 'collection') {
                    $resultTo = 'copy';
                } elseif ($type == 'array') {
                    $resultTo = 'toArray';
                } elseif ($type == 'json') {
                    $resultTo = 'toJson';
                } else {
                    $resultTo = 'toArray';
                }
            } else {
                $type = registry()->get('flextype.settings.entries.macros.entries.fetch.type');
                if ($type == 'collection') {
                    $resultTo = 'copy';
                } elseif ($type == 'array') {
                    $resultTo = 'toArray';
                } elseif ($type == 'json') {
                    $resultTo = 'toJson';
                } else {
                    $resultTo = 'toArray';
                }
            }
            $data[$key] = entries()->fetch($value['id'], isset($value['options']) ? $value['options'] : [])->{$resultTo}();
        }

        // Restore original entry data and merge new data.
        entries()->registry()->set('methods.fetch.params.id', $original['params']['id']);
        entries()->registry()->set('methods.fetch.params.options', $original['params']['options']);
        entries()->registry()->set('methods.fetch.result', collection($original['result'])->merge($data)->toArray());
    }
});