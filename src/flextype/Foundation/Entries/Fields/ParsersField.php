<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.parsers.enabled')) {
    flextype('emitter')->addListener('onEntriesFetchSingleHasResult', static function (): void {
        processParsersField();
    });
}

function processParsersField(): void
{
    if (flextype('entries')->storage()->get('fetch.data.cache.enabled') == null) {
        $cache = false;
    } else {
        $cache = (bool) flextype('entries')->storage()->get('fetch.data.cache.enabled');
    }

    if (flextype('entries')->storage()->get('fetch.data.parsers') != null) {
        foreach (flextype('entries')->storage()->get('fetch.data.parsers') as $parserName => $parserData) {
            if (in_array($parserName, ['markdown', 'shortcode'])) {
                if (flextype('entries')->storage()->get('fetch.data.parsers.'.$parserName.'.enabled') === true) {
                    if (flextype('entries')->storage()->get('fetch.data.parsers.'.$parserName.'.fields') != null) {
                        if (is_array(flextype('entries')->storage()->get('fetch.data.parsers.'.$parserName.'.fields'))) {
                            foreach (flextype('entries')->storage()->get('fetch.data.parsers.'.$parserName.'.fields') as $field) {
                                if (! in_array($field, flextype('registry')->get('flextype.settings.entries.fields'))) {
                                    if ($parserName == 'markdown') {
                                        if (arrays(flextype('entries')->storage()->get('fetch.data'))->has($field)) {
                                            flextype('entries')->storage()->set('fetch.data.'.$field,
                                                                            flextype('parsers')->markdown()->parse(flextype('entries')->storage()->get('fetch.data.'.$field), $cache));
                                        }
                                    }
                                    if ($parserName == 'shortcode') {
                                        if (arrays(flextype('entries')->storage()->get('fetch.data'))->has($field)) {
                                            flextype('entries')->storage()->set('fetch.data.'.$field,
                                                                            flextype('parsers')->shortcode()->process(flextype('entries')->storage()->get('fetch.data.'.$field), $cache));
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
}
