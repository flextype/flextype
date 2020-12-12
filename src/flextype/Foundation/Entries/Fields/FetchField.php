<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.fetch.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {

        if (flextype('entries')->getStorage('fetch_single.data.fetch') !== null) {
            $original = flextype('entries')->getStorage('fetch_single');

            $fetch = flextype('entries')->getStorage('fetch_single.data.fetch');

            foreach ($fetch as $field => $body) {
                $data[$field] = flextype('entries')->fetch($body['id'], $body['from'], $body['options']);
            }

            flextype('entries')->setStorage('fetch_single.id', $original['id']);
            flextype('entries')->setStorage('fetch_single.options', $original['options']);
            flextype('entries')->setStorage('fetch_single.data', arrays($original['data'])->merge($data)->toArray());
        }
    });
}

flextype('entries')::macro('fetchFromAnotherDB', function($id, $options) {
    return [$id, $options];
});
