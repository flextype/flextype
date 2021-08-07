<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

emitter()->addListener('onContentFetchSingleHasResult', static function (): void {
    
    if (registry()->get('flextype.settings.entries.content.fields.parsers.enabled')) {
        return;
    }

    if (content()->registry()->get('fetch.data.cache.enabled') == null) {
        $cache = false;
    } else {
        $cache = (bool) content()->registry()->get('fetch.data.cache.enabled');
    }

    if (content()->registry()->get('fetch.data.parsers') != null) {
        
        foreach (content()->registry()->get('fetch.data.parsers') as $parserName => $parserData) {
            if (in_array($parserName, ['shortcodes', 'markdown'])) {

                if (content()->registry()->get('fetch.data.parsers.'.$parserName.'.enabled') === true) {
                    if (content()->registry()->get('fetch.data.parsers.'.$parserName.'.fields') != null) {
                        if (is_array(content()->registry()->get('fetch.data.parsers.'.$parserName.'.fields'))) {
                            foreach (content()->registry()->get('fetch.data.parsers.'.$parserName.'.fields') as $field) {
                                if (! in_array($field, registry()->get('flextype.settings.entries.content.fields'))) {
                                    if ($parserName == 'markdown') {
                                        if (arrays(content()->registry()->get('fetch.data'))->has($field)) {
                                            content()->registry()->set('fetch.data.'.$field,
                                                                            parsers()->markdown()->parse(content()->registry()->get('fetch.data.'.$field), $cache));
                                        }
                                    }
                                    
                                    if ($parserName == 'shortcodes') {
                                        if (arrays(content()->registry()->get('fetch.data'))->has($field)) {
                                            content()->registry()->set('fetch.data.'.$field,
                                                                            parsers()->shortcodes()->parse(content()->registry()->get('fetch.data.'.$field), $cache));
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
