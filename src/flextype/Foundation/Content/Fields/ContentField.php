<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Arrays\Arrays;

if (flextype('registry')->get('flextype.settings.entries.content.fields.entries.fetch.enabled')) {
     flextype('emitter')->addListener('onContentFetchSingleHasResult', static function (): void {
         if (flextype('content')->registry()->has('fetch.data.entries.fetch')) {
             // Get fetch.
             $original = flextype('content')->registry()->get('fetch');
             $data = [];

             switch (flextype('registry')->get('flextype.settings.entries.content.fields.content.fetch.result')) {
                 case 'toArray':
                     $resultTo = 'toArray';
                     break;

                 case 'toObject':
                 default:
                     $resultTo = 'copy';
                     break;
             }

             // Modify fetch.
             foreach (flextype('content')->registry()->get('fetch.data.content.fetch') as $field => $body) {

                 if (isset($body['options']['method']) &&
                     strpos($body['options']['method'], 'fetch') !== false &&
                     is_callable([flextype('content'), $body['options']['method']])) {
                     $fetchFromCallbackMethod = $body['options']['method'];
                 } else {
                     $fetchFromCallbackMethod = 'fetch';
                 }

                 $result = isset($body['result']) && in_array($body['result'], ['toArray', 'toObject']) ? $body['result'] : $resultTo;

                 $data[$field] = flextype('content')->{$fetchFromCallbackMethod}($body['id'],
                                                            isset($body['options']) ?
                                                                  $body['options'] :
                                                                  []);

                $data[$field] = ($data[$field] instanceof Arrays) ? $data[$field]->{$result}() : $data[$field];
             }

             // Save fetch.
             flextype('content')->registry()->set('fetch.id', $original['id']);
             flextype('content')->registry()->set('fetch.options', $original['options']);
             flextype('content')->registry()->set('fetch.data', arrays($original['data'])->merge($data)->toArray());
         }
     });
}
