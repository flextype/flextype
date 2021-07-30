<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (registry()->get('flextype.settings.entries.fields.registry.get.enabled')) {
    emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (entries()->registry()->has('fetch.data.registry.get')) {
            // Get fetch.
            $original = entries()->registry()->get('fetch');

            $data = [];

            // Modify fetch.
            foreach (entries()->registry()->get('fetch.data.registry.get') as $field => $body) {
                $data = arrays($data)->merge(arrays($data)->set($field, registry()->get($body['key'],
                                                          isset($body['default']) ?
                                                                $body['default'] :
                                                                []))->toArray())->toArray();

            }

            // Save fetch.
            entries()->registry()->set('fetch.data', arrays($original['data'])->merge($data)->toArray());
        }
    });
}
