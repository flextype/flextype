<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (registry()->get('flextype.settings.entries.fields.parsers.enabled')) {
    emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {
        
        if (entries()->registry()->get('fetch.data.cache.enabled') == null) {
            $cache = false;
        } else {
            $cache = (bool) entries()->registry()->get('fetch.data.cache.enabled');
        }
    
        if (entries()->registry()->get('fetch.data.parsers') != null) {
           
            foreach (entries()->registry()->get('fetch.data.parsers') as $parserName => $parserData) {
                if (in_array($parserName, ['shortcodes'])) {
    
                    if (entries()->registry()->get('fetch.data.parsers.'.$parserName.'.enabled') === true) {
                        if (entries()->registry()->get('fetch.data.parsers.'.$parserName.'.fields') != null) {
                            if (is_array(entries()->registry()->get('fetch.data.parsers.'.$parserName.'.fields'))) {
                                foreach (entries()->registry()->get('fetch.data.parsers.'.$parserName.'.fields') as $field) {
                                    if (! in_array($field, registry()->get('flextype.settings.entries.fields'))) {
                                        if ($parserName == 'shortcodes') {
                                            if (arrays(entries()->registry()->get('fetch.data'))->has($field)) {
                                                entries()->registry()->set('fetch.data.'.$field,
                                                                                parsers()->shortcodes()->parse(entries()->registry()->get('fetch.data.'.$field), $cache));
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
}
