<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Arrays\Arrays;

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('collection.options.fields.entries.enabled')) {
        return;
    }

    if (entries()->registry()->has('fetch.data.entries.fetch')) {
        // Get fetch.
        $original = entries()->registry()->get('fetch');
        $data = [];

        switch (registry()->get('flextype.settings.entries.collections.default.fields.entries.fetch.result')) {
            case 'toArray':
                $resultTo = 'toArray';
                break;

            case 'toObject':
            default:
                $resultTo = 'copy';
                break;
        }

        // Modify fetch.
        foreach (entries()->registry()->get('fetch.data.entries.fetch') as $field => $body) {

            if (isset($body['options']['method']) &&
                strpos($body['options']['method'], 'fetch') !== false &&
                is_callable([content(), $body['options']['method']])) {
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

        // Save fetch.
        entries()->registry()->set('fetch.id', $original['id']);
        entries()->registry()->set('fetch.options', $original['options']);
        entries()->registry()->set('fetch.data', arrays($original['data'])->merge($data)->toArray());
    }
});