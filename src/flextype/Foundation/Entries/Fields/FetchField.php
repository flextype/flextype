<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.fetch.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        if (flextype('entries')->getStorage('fetch.data.fetch') !== null) {
            $original = flextype('entries')->getStorage('fetch');
            foreach (flextype('entries')->getStorage('fetch.data.fetch') as $fetch) {
                if (isset($fetch['options']) && isset($fetch['options']['collection']) && strings($fetch['options']['collection'])->isTrue()) {
                    $data[$fetch['result']] = flextype('entries')->fetchCollection($fetch['id'], $fetch['options']);
                } else {
                    $data[$fetch['result']] = flextype('entries')->fetchSingle($fetch['id']);
                }
            }
            flextype('entries')->setStorage('fetch.data', arrays($original['data'])->merge($data)->toArray());
        }
    });
}
