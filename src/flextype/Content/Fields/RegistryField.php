<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onContentFetchSingleHasResult', static function (): void {

    if (! registry()->get('flextype.settings.entries.content.fields.registry.enabled')) {
        return;
    }

    if (content()->registry()->has('fetch.data.registry.get')) {
        // Get fetch.
        $original = content()->registry()->get('fetch');

        $data = [];

        // Modify fetch.
        foreach (content()->registry()->get('fetch.data.registry.get') as $field => $body) {
            $data = arrays($data)->merge(arrays($data)->set($field, registry()->get($body['key'],
                                                        isset($body['default']) ?
                                                            $body['default'] :
                                                            []))->toArray())->toArray();

        }

        // Save fetch.
        content()->registry()->set('fetch.data', arrays($original['data'])->merge($data)->toArray());
    }
});