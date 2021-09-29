<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('methods.fetch.collection.fields.registry.enabled')) {
        return;
    }

    if (entries()->registry()->has('methods.fetch.result.registry.get')) {

        // Get fetch.
        $original = entries()->registry()->get('methods.fetch');

        $data = [];

        // Modify fetch.
        foreach (entries()->registry()->get('methods.fetch.result.registry.get') as $field => $body) {
            $data = arrays($data)->merge(arrays($data)->set($field, registry()->get($body['key'],
                                                        isset($body['default']) ?
                                                            $body['default'] :
                                                            []))->toArray())->toArray();

        }

        $result = arrays($original['result'])->merge($data)->toArray();

        if (boolval(entries()->registry()->get('methods.fetch.collection.fields.entries.dump')) === false) {
            unset($result['registry']);
        }

        // Save fetch.
        entries()->registry()->set('methods.fetch.result', $result);
    }
});