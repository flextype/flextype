<?php

declare(strict_types=1);

/**
 * Flextype (https://awilum.github.io/flextype)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Glowy\Arrays\Arrays;

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('methods.fetch.collection.fields.entries.enabled')) {
        return;
    }

    if (entries()->registry()->has('methods.fetch.result.entries.fetch')) {

        // Get 
        $original = entries()->registry()->get('methods.fetch');
        $data = [];

        switch (entries()->registry()->get('methods.fetch.collection.fields.entries.result')) {
            case 'toArray':
                $resultTo = 'toArray';
                break;

            case 'toObject':
            default:
                $resultTo = 'copy';
                break;
        }

        // Modify 
        foreach (entries()->registry()->get('methods.fetch.result.entries.fetch') as $field => $body) {

            if (isset($body['options']['method']) &&
                strpos($body['options']['method'], 'fetch') !== false &&
                is_callable([entries(), $body['options']['method']])) {
                $fetchFromCallbackMethod = $body['options']['method'];
            } else {
                $fetchFromCallbackMethod = 'fetch';
            }

            $result = isset($body['result']) && in_array($body['result'], ['toArray', 'toObject']) ? $body['result'] : $resultTo;

            $data[$field] = entries()->{$fetchFromCallbackMethod}($body['id'],
                                                    isset($body['options']) ?
                                                            $body['options'] :
                                                            []);


            $data[$field] = ($data[$field] instanceof Arrays) ? $data[$field]->{$result}() : $data[$field];
        }

        $result = arrays($original['result'])->merge($data)->toArray();

        if (boolval(entries()->registry()->get('methods.fetch.collection.fields.entries.dump')) === false) {
            unset($result['entries']);
        }

        // Save fetch data.
        entries()->registry()->set('methods.fetch.params.id', $original['params']['id']);
        entries()->registry()->set('methods.fetch.params.options', $original['params']['options']);
        entries()->registry()->set('methods.fetch.result', $result);
        
    }
});