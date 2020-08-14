<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Arrays\Arrays;

if ($flextype->container('registry')->get('flextype.settings.entries.fields.parsers.enabled')) {
    $flextype->container('emitter')->addListener('onEntryAfterInitialized', static function () use ($flextype) : void {
        processParsersField($flextype);
    });
}

function processParsersField($flextype) : void
{
    $cache = $flextype->container('entries')->entry['cache']['enabled'] ??
                        $flextype->container('registry')->get('flextype.settings.cache.enabled');

    if (! isset($flextype->container('entries')->entry['parsers'])) {
        return;
    }

    foreach ($flextype->container('entries')->entry['parsers'] as $parser_name => $parser_data) {
        if (! in_array($parser_name, ['markdown', 'shortcode'])) {
            continue;
        }

        if (! isset($flextype->container('entries')->entry['parsers'][$parser_name]['enabled']) || $flextype->container('entries')->entry['parsers'][$parser_name]['enabled'] !== true) {
            continue;
        }

        if (! isset($flextype->container('entries')->entry['parsers'][$parser_name]['fields'])) {
            continue;
        }

        if (! is_array($flextype->container('entries')->entry['parsers'][$parser_name]['fields'])) {
            continue;
        }

        foreach ($flextype->container('entries')->entry['parsers'][$parser_name]['fields'] as $field) {
            if (in_array($field, $flextype->container('registry')->get('flextype.settings.entries.fields'))) {
                continue;
            }

            if ($parser_name === 'markdown') {
                if (Arrays::has($flextype->container('entries')->entry, $field)) {
                    Arrays::set($flextype->container('entries')->entry, $field, $flextype->markdown->parse(Arrays::get($flextype->container('entries')->entry, $field), $cache));
                }
            }

            if ($parser_name !== 'shortcode') {
                continue;
            }

            if (! Arrays::has($flextype->container('entries')->entry, $field)) {
                continue;
            }

            Arrays::set($flextype->container('entries')->entry, $field, $flextype->shortcode->parse(Arrays::get($flextype->container('entries')->entry, $field), $cache));
        }
    }
}
