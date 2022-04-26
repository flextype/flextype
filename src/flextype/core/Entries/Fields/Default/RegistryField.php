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

    if (! entries()->registry()->get('methods.fetch.collection.fields.registry.enabled')) {
        return;
    }

    if (entries()->registry()->has('methods.fetch.result.registry.get')) {

        // Get fetch.
        $original = entries()->registry()->get('methods.fetch');

        $data = [];

        // Modify fetch.
        foreach (entries()->registry()->get('methods.fetch.result.registry.get') as $field => $body) {
            $data = collection($data)->merge(collection($data)->set($field, registry()->get($body['id'],
                                                        isset($body['default']) ?
                                                            $body['default'] :
                                                            []))->toArray())->toArray();

        }

        $result = collection($original['result'])->merge($data)->toArray();

        if (boolval(entries()->registry()->get('methods.fetch.collection.fields.registry.dump')) === false) {
            unset($result['registry']);
        }

        // Save fetch.
        entries()->registry()->set('methods.fetch.result', $result);
    }
});