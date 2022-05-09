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

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! registry()->get('flextype.settings.entries.macros.entries.enabled')) {
        return;
    }
    
    if (entries()->registry()->has('methods.fetch.result.macros.entries.fetch') &&
        registry()->get('flextype.settings.entries.macros.entries.fetch.enabled') === true) {

        // Get 
        $original = entries()->registry()->get('methods.fetch');
        $data = [];

        switch (registry()->get('flextype.settings.entries.macros.entries.result')) {
            case 'toArray':
                $resultTo = 'toArray';
                break;

            case 'toObject':
                $resultTo = 'copy';
                break;

            default:
                if (registry()->has('flextype.settings.entries.macros.entries.fetch.result')) {
                    if (in_array(registry()->get('flextype.settings.entries.macros.entries.fetch.result'), ['toArray', 'toObject'])) {
                        $resultTo = registry()->get('flextype.settings.entries.macros.entries.fetch.result');

                        if ($resultTo == 'toObject') {
                            $resultTo = 'copy';
                        }
                    }
                } else {
                    $resultTo = 'copy';
                }
                break;
        }

        // Modify data
        foreach (entries()->registry()->get('methods.fetch.result.macros.entries.fetch') as $field => $body) {
            $result = isset($body['result']) && in_array($body['result'], ['toArray', 'toObject']) ? $body['result'] == 'toObject' ? 'copy' : 'toArray' : $resultTo;
            $data[$field] = entries()->fetch($body['id'], isset($body['options']) ? $body['options'] : []);
            $data[$field] = ($data[$field] instanceof Collection) ? $data[$field]->{$result}() : $data[$field];
        }

        $result = collection($original['result'])->merge($data)->toArray();

        // Save fetch data.
        entries()->registry()->set('methods.fetch.params.id', $original['params']['id']);
        entries()->registry()->set('methods.fetch.params.options', $original['params']['options']);
        entries()->registry()->set('methods.fetch.result', $result);
    }
});