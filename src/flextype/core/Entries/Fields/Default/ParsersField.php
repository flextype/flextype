<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {

    if (! entries()->registry()->get('collection.options.fields.parsers.enabled')) {
        return;
    }

    if (entries()->registry()->get('fetch.result.cache.enabled') == null) {
        $cache = false;
    } else {
        $cache = (bool) entries()->registry()->get('fetch.result.cache.enabled');
    }

    if (entries()->registry()->get('fetch.result.parsers') != null) {
        
        foreach (entries()->registry()->get('fetch.result.parsers') as $parserName => $parserData) {
            if (in_array($parserName, ['shortcodes', 'markdown'])) {

                if (entries()->registry()->get('fetch.result.parsers.'.$parserName.'.enabled') === true) {
                    if (entries()->registry()->get('fetch.result.parsers.'.$parserName.'.fields') != null) {
                        if (is_array(entries()->registry()->get('fetch.result.parsers.'.$parserName.'.fields'))) {
                            foreach (entries()->registry()->get('fetch.result.parsers.'.$parserName.'.fields') as $field) {
                                if (! in_array($field, registry()->get('flextype.settings.entries.collections.default.fields'))) {
                                    if ($parserName == 'markdown') {
                                        if (arrays(entries()->registry()->get('fetch.result'))->has($field)) {
                                            entries()->registry()->set('fetch.result.'.$field,
                                                                            parsers()->markdown()->parse(entries()->registry()->get('fetch.result.'.$field), $cache));
                                        }
                                    }
                                    
                                    if ($parserName == 'shortcodes') {
                                        if (arrays(entries()->registry()->get('fetch.result'))->has($field)) {
                                            entries()->registry()->set('fetch.result.'.$field,
                                                                            parsers()->shortcodes()->parse(entries()->registry()->get('fetch.result.'.$field), $cache));
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
