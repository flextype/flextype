<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.registry.get.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->storage()->has('fetch.data.registry.get')) {
            // Get fetch.
            $original = flextype('entries')->storage()->get('fetch');

            $data = [];

            // Modify fetch.
            foreach (flextype('entries')->storage()->get('fetch.data.registry.get') as $field => $body) {
                $data = arrays($data)->merge(arrays($data)->set($field, flextype('registry')->get($body['key'],
                                                          isset($body['default']) ?
                                                                $body['default'] :
                                                                []))->toArray())->toArray();

            }

            // Save fetch.
            flextype('entries')->storage()->set('fetch.data', arrays($original['data'])->merge($data)->toArray());
        }
    });
}
