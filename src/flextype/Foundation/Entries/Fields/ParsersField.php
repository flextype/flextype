<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (flextype('registry')->get('flextype.settings.entries.fields.parsers.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function (): void {
        processParsersField();
    });
}

function processParsersField(): void
{
    if (flextype('entries')->getStorage('fetch.data.cache.enabled') == null) {
        $cache = false;
    } else {
        $cache = (bool) flextype('entries')->getStorage('fetch.data.cache.enabled');
    }

    if (flextype('entries')->getStorage('fetch.data.parsers') != null) {
        foreach (flextype('entries')->getStorage('fetch.data.parsers') as $parser_name => $parser_data) {
            if (in_array($parser_name, ['markdown', 'shortcode'])) {
                if (flextype('entries')->getStorage('fetch.data.parsers.'.$parser_name.'.enabled') === true) {
                    if (flextype('entries')->getStorage('fetch.data.parsers.'.$parser_name.'.fields') != null) {
                        if (is_array(flextype('entries')->getStorage('fetch.data.parsers.'.$parser_name.'.fields'))) {
                            foreach (flextype('entries')->getStorage('fetch.data.parsers.'.$parser_name.'.fields') as $field) {
                                if (! in_array($field, flextype('registry')->get('flextype.settings.entries.fields'))) {
                                    if ($parser_name == 'markdown') {
                                        if (arrays(flextype('entries')->getStorage('fetch.data'))->has($field)) {
                                            flextype('entries')->setStorage('fetch.data.'.$field,
                                                                            flextype('markdown')->parse(flextype('entries')->getStorage('fetch.data.'.$field), $cache));
                                        }
                                    }
                                    if ($parser_name == 'shortcode') {
                                        if (arrays(flextype('entries')->getStorage('fetch.data'))->has($field)) {
                                            flextype('entries')->setStorage('fetch.data.'.$field,
                                                                            flextype('shortcode')->process(flextype('entries')->getStorage('fetch.data.'.$field), $cache));
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
