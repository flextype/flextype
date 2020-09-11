<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Arrays\Arrays;

if (flextype('registry')->get('flextype.settings.entries.fields.parsers.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function (): void {
        processParsersField();
    });
}

function processParsersField(): void
{
    if (flextype('entries')->getStorage('fetch_single.data.cache.enabled') == null) {
        $cache = false;
    } else {
        $cache = (bool) flextype('entries')->getStorage('fetch_single.data.cache.enabled');
    }

    if (flextype('entries')->getStorage('fetch_single.data.parsers') != null) {
        foreach (flextype('entries')->getStorage('fetch_single.data.parsers') as $parser_name => $parser_data) {
            if (in_array($parser_name, ['markdown', 'shortcode'])) {
                if (flextype('entries')->getStorage('fetch_single.data.parsers.'.$parser_name.'.enabled') === true) {
                    if (flextype('entries')->getStorage('fetch_single.data.parsers.'.$parser_name.'.fields') != null) {
                        if (is_array(flextype('entries')->getStorage('fetch_single.data.parsers.'.$parser_name.'.fields'))) {
                            foreach (flextype('entries')->getStorage('fetch_single.data.parsers.'.$parser_name.'.fields') as $field) {
                                if (! in_array($field, flextype('registry')->get('flextype.settings.entries.fields'))) {
                                    if ($parser_name == 'markdown') {
                                        if (Arrays::has(flextype('entries')->getStorage('fetch_single.data'), $field)) {
                                            flextype('entries')->setStorage('fetch_single.data.'.$field,
                                                                            flextype('markdown')->parse(flextype('entries')->getStorage('fetch_single.data.'.$field), $cache));
                                        }
                                    }
                                    if ($parser_name == 'shortcode') {
                                        if (Arrays::has(flextype('entries')->getStorage('fetch_single.data'), $field)) {
                                            flextype('entries')->setStorage('fetch_single.data.'.$field,
                                                                            flextype('shortcode')->process(flextype('entries')->getStorage('fetch_single.data.'.$field), $cache));
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
