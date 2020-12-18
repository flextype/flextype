<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Arrays\Arrays;

if (flextype('registry')->get('flextype.settings.entries.fields.media.files.fetch.enabled')) {
     flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
         if (flextype('entries')->hasStorage('fetch.data.media.files.fetch')) {
             // Get fetch.
             $original = flextype('entries')->getStorage('fetch');

             switch (flextype('registry')->get('flextype.settings.entries.fields.media.files.fetch.result')) {
                 case 'toArray':
                     $resultTo = 'toArray';
                     break;

                 case 'toObject':
                 default:
                     $resultTo = 'copy';
                     break;
             }

             // Modify fetch.
             foreach (flextype('entries')->getStorage('fetch.data.media.files.fetch') as $field => $body) {

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
             flextype('entries')->setStorage('fetch.id', $original['id']);
             flextype('entries')->setStorage('fetch.options', $original['options']);
             flextype('entries')->setStorage('fetch.data', arrays($original['data'])->merge($data)->toArray());
         }
     });
}


if (flextype('registry')->get('flextype.settings.entries.fields.media.folders.fetch.enabled')) {
     flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
         if (flextype('entries')->hasStorage('fetch.data.media.folders.fetch')) {

             // Get fetch.
             $original = flextype('entries')->getStorage('fetch');

             switch (flextype('registry')->get('flextype.settings.entries.fields.media.folders.fetch.result')) {
                 case 'toArray':
                     $resultTo = 'toArray';
                     break;

                 case 'toObject':
                 default:
                     $resultTo = 'copy';
                     break;
             }

             // Modify fetch.
             foreach (flextype('entries')->getStorage('fetch.data.media.folders.fetch') as $field => $body) {

                 if (isset($body['options']['method']) &&
                     strpos($body['options']['method'], 'fetch') !== false &&
                     is_callable([flextype('media')->folders(), $body['options']['method']])) {
                     $fetchFromCallbackMethod = $body['options']['method'];
                 } else {
                     $fetchFromCallbackMethod = 'fetch';
                 }


                 $result = isset($body['result']) && in_array($body['result'], ['toArray', 'toObject']) ? $body['result'] : $resultTo;

                 $data[$field] = flextype('media')->folders()->{$fetchFromCallbackMethod}($body['id'],
                                                            isset($body['options']) ?
                                                                  $body['options'] :
                                                                  []);

                $data[$field] = ($data[$field] instanceof Arrays) ? $data[$field]->{$result}() : $data[$field];
             }

             // Save fetch.
             flextype('entries')->setStorage('fetch.id', $original['id']);
             flextype('entries')->setStorage('fetch.options', $original['options']);
             flextype('entries')->setStorage('fetch.data', arrays($original['data'])->merge($data)->toArray());
         }
     });
}
