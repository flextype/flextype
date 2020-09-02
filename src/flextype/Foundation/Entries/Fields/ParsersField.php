<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Arrays\Arrays;

if (flextype('registry')->get('flextype.settings.entries.fields.parsers.enabled')) {
    flextype('emitter')->addListener('onEntryAfterInitialized', static function () : void {
        processParsersField();
    });
}

function processParsersField() : void
{
    $cache = flextype('entries')->entry['cache']['enabled'] ??
                        flextype('registry')->get('flextype.settings.cache.enabled');

    if (! isset(flextype('entries')->entry['parsers'])) {
        return;
    }

    foreach (flextype('entries')->entry['parsers'] as $parser_name => $parser_data) {
        if (! in_array($parser_name, ['markdown', 'shortcode'])) {
            continue;
        }

        if (! isset(flextype('entries')->entry['parsers'][$parser_name]['enabled']) || flextype('entries')->entry['parsers'][$parser_name]['enabled'] !== true) {
            continue;
        }

        if (! isset(flextype('entries')->entry['parsers'][$parser_name]['fields'])) {
            continue;
        }

        if (! is_array(flextype('entries')->entry['parsers'][$parser_name]['fields'])) {
            continue;
        }

        foreach (flextype('entries')->entry['parsers'][$parser_name]['fields'] as $field) {
            if (in_array($field, flextype('registry')->get('flextype.settings.entries.fields'))) {
                continue;
            }

            if ($parser_name === 'markdown') {
                if (Arrays::has(flextype('entries')->entry, $field)) {
                    Arrays::set(flextype('entries')->entry, $field, flextype('markdown')->parse(Arrays::get(flextype('entries')->entry, $field), $cache));
                }
            }

            if ($parser_name !== 'shortcode') {
                continue;
            }

            if (! Arrays::has(flextype('entries')->entry, $field)) {
                continue;
            }

            Arrays::set(flextype('entries')->entry, $field, flextype('shortcode')->parse(Arrays::get(flextype('entries')->entry, $field), $cache));
        }
    }
}
