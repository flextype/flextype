<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Arrays\Arrays;

emitter()->addListener('onContentFetchSingleHasResult', static function (): void {
   
    if (! registry()->get('flextype.settings.entries.content.fields.media.enabled')) {
        return;
    }

    if (content()->registry()->has('fetch.data.media.fetch')) {
        // Get fetch.
        $original = content()->registry()->get('fetch');
        $data = [];

        switch (registry()->get('flextype.settings.entries.content.fields.media.files.fetch.result')) {
            case 'toArray':
                $resultTo = 'toArray';
                break;

            case 'toObject':
            default:
                $resultTo = 'copy';
                break;
        }

        // Modify fetch.
        foreach (content()->registry()->get('fetch.data.media.files.fetch') as $field => $body) {

            if (isset($body['options']['method']) &&
                strpos($body['options']['method'], 'fetch') !== false &&
                is_callable([flextype('media')->files(), $body['options']['method']])) {
                $fetchFromCallbackMethod = $body['options']['method'];
            } else {
                $fetchFromCallbackMethod = 'fetch';
            }


            $result = isset($body['result']) && in_array($body['result'], ['toArray', 'toObject']) ? $body['result'] : $resultTo;

            $data[$field] = flextype('media')->files()->{$fetchFromCallbackMethod}($body['id'],
                                                    isset($body['options']) ?
                                                            $body['options'] :
                                                            []);

        $data[$field] = ($data[$field] instanceof Arrays) ? $data[$field]->{$result}() : $data[$field];
        }

        // Save fetch.
        content()->registry()->set('fetch.id', $original['id']);
        content()->registry()->set('fetch.options', $original['options']);
        content()->registry()->set('fetch.data', arrays($original['data'])->merge($data)->toArray());
    }
});