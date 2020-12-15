<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.registry.get.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->hasStorage('fetch.data.registry.get')) {
            // Get fetch.
            $original = flextype('entries')->getStorage('fetch');

            $data = [];

            // Modify fetch.
            foreach (flextype('entries')->getStorage('fetch.data.registry.get') as $field => $body) {
                $data = arrays($data)->merge(arrays($data)->set($field, flextype('registry')->get($body['key'],
                                                          isset($body['default']) ?
                                                                $body['default'] :
                                                                []))->toArray())->toArray();

            }

            // Save fetch.
            flextype('entries')->setStorage('fetch.data', arrays($original['data'])->merge($data)->toArray());
        }
    });
}
