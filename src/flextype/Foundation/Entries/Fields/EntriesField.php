<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.entries.fetchCollection.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->hasStorage('fetch.data.entries.fetchCollection')) {
            // Get fetch.
            $original = flextype('entries')->getStorage('fetch');

            switch (flextype('registry')->get('flextype.settings.entries.fields.entries.fetchCollection.result')) {
                case 'toArray':
                    $resultTo = 'toArray';
                    break;

                case 'toObject':
                default:
                    $resultTo = 'copy';
                    break;
            }

            // Modify fetch.
            foreach (flextype('entries')->getStorage('fetch.data.entries.fetchCollection') as $field => $body) {
                $data[$field] = flextype('entries')->fetchCollection($body['id'],
                                                                     isset($body['options']) ?
                                                                           $body['options'] :
                                                                           [])->{$resultTo}();
            }

            // Save fetch.
            flextype('entries')->setStorage('fetch.id', $original['id']);
            flextype('entries')->setStorage('fetch.options', $original['options']);
            flextype('entries')->setStorage('fetch.data', arrays($original['data'])->merge($data)->toArray());
        }
    });
}

if (flextype('registry')->get('flextype.settings.entries.fields.entries.fetchSingle.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->hasStorage('fetch.data.entries.fetchSingle')) {
            // Get fetch.
            $original = flextype('entries')->getStorage('fetch');

            switch (flextype('registry')->get('flextype.settings.entries.fields.entries.fetchSingle.result')) {
                case 'toArray':
                    $resultTo = 'toArray';
                    break;

                case 'toObject':
                default:
                    $resultTo = 'copy';
                    break;
            }

            // Modify fetch.
            foreach (flextype('entries')->getStorage('fetch.data.entries.fetchSingle') as $field => $body) {
                $data[$field] = flextype('entries')->fetchSingle($body['id'],
                                                                 isset($body['options']) ?
                                                                       $body['options'] :
                                                                       [])->{$resultTo}();
            }

            // Save fetch.
            flextype('entries')->setStorage('fetch.id', $original['id']);
            flextype('entries')->setStorage('fetch.options', $original['options']);
            flextype('entries')->setStorage('fetch.data', arrays($original['data'])->merge($data)->toArray());
        }
    });
}
