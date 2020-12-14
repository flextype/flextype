<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.fetch.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {

        if (flextype('entries')->getStorage('fetch.data.fetch') !== null) {

            // Get fetch.
            $original = flextype('entries')->getStorage('fetch');

            // Modify fetch.
            foreach (flextype('entries')->getStorage('fetch.data.fetch') as $field => $body) {
                $data[$field] = flextype('entries')->fetch($body['id'],
                                                           $body['from'],
                                                           isset($body['options']) ?
                                                                 $body['options'] :
                                                                 []);
            }

            // Save fetch.
            flextype('entries')->setStorage('fetch.id', $original['id']);
            flextype('entries')->setStorage('fetch.from', $original['from']);
            flextype('entries')->setStorage('fetch.options', $original['options']);
            flextype('entries')->setStorage('fetch.data', arrays($original['data'])->merge($data)->toArray());
        }
    });
}
