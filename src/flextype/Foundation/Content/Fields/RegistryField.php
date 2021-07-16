<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.content.fields.registry.get.enabled')) {
    flextype('emitter')->addListener('onContentFetchSingleHasResult', static function (): void {
        if (flextype('content')->registry()->has('fetch.data.registry.get')) {
            // Get fetch.
            $original = flextype('content')->registry()->get('fetch');

            $data = [];

            // Modify fetch.
            foreach (flextype('content')->registry()->get('fetch.data.registry.get') as $field => $body) {
                $data = arrays($data)->merge(arrays($data)->set($field, flextype('registry')->get($body['key'],
                                                          isset($body['default']) ?
                                                                $body['default'] :
                                                                []))->toArray())->toArray();

            }

            // Save fetch.
            flextype('content')->registry()->set('fetch.data', arrays($original['data'])->merge($data)->toArray());
        }
    });
}
