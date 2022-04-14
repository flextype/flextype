<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS 
 * and with the full functionality of a traditional CMS!
 * 
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('methods.fetch.collection.fields.parsers.enabled')) {
        return;
    }

    if (entries()->registry()->get('methods.fetch.result.cache.enabled') == null) {
        $cache = false;
    } else {
        $cache = (bool) entries()->registry()->get('methods.fetch.result.cache.enabled');
    }

    if (entries()->registry()->get('methods.fetch.result.parsers') != null) {
        
        foreach (entries()->registry()->get('methods.fetch.result.parsers') as $parserName => $parserData) {
            if (in_array($parserName, ['shortcodes', 'markdown'])) {

                if (entries()->registry()->get('methods.fetch.result.parsers.'.$parserName.'.enabled') === true) {
                    if (entries()->registry()->get('methods.fetch.result.parsers.'.$parserName.'.fields') != null) {
                        if (is_array(entries()->registry()->get('methods.fetch.result.parsers.'.$parserName.'.fields'))) {
                            foreach (entries()->registry()->get('methods.fetch.result.parsers.'.$parserName.'.fields') as $field) {
                                if (! in_array($field, registry()->get('flextype.settings.entries.collections.default.fields'))) {
                                    if ($parserName == 'markdown') {
                                        if (collection(entries()->registry()->get('methods.fetch.result'))->has($field)) {
                                            entries()->registry()->set('methods.fetch.result.'.$field,
                                                                            parsers()->markdown()->parse(entries()->registry()->get('methods.fetch.result.'.$field), $cache));
                                        }
                                    }
                                    
                                    if ($parserName == 'shortcodes') {
                                        if (collection(entries()->registry()->get('methods.fetch.result'))->has($field)) {
                                            entries()->registry()->set('methods.fetch.result.'.$field, 
                                                                       parsers()->shortcodes()->parse(entries()->registry()->get('methods.fetch.result.'.$field), $cache));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
});
