<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('collection.options.fields.registry.enabled')) {
        return;
    }

    if (entries()->registry()->has('fetch.result.registry.get')) {

        // Get fetch.
        $original = entries()->registry()->get('fetch');

        $data = [];

        // Modify fetch.
        foreach (entries()->registry()->get('fetch.result.registry.get') as $field => $body) {
            $data = arrays($data)->merge(arrays($data)->set($field, registry()->get($body['key'],
                                                        isset($body['default']) ?
                                                            $body['default'] :
                                                            []))->toArray())->toArray();

        }

        $result = arrays($original['result'])->merge($data)->toArray();

        if (boolval(entries()->registry()->get('collection.options.fields.entries.dump')) === false) {
            unset($result['registry']);
        }

        // Save fetch.
        entries()->registry()->set('fetch.result', $result);
    }
});