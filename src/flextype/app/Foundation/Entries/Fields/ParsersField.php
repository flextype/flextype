<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Component\Arrays\Arrays;

if ($container->registry->get('flextype.settings.entries.fields.parsers.enabled')) {
    $container->emitter->addListener('onEntryAfterInitialized', function () use ($container) : void {
        processParsersField($container);
    });
}

function processParsersField($container) : void
{
    $cache = isset($container->entries->entry['cache']['enabled']) ?
                        $container->entries->entry['cache']['enabled'] :
                        $container->registry->get('flextype.settings.cache.enabled');

    if (isset($container->entries->entry['parsers'])) {
        foreach ($container->entries->entry['parsers'] as $parser_name => $parser_data) {
            if (in_array($parser_name, ['markdown', 'shortcode'])) {
                if (isset($container->entries->entry['parsers'][$parser_name]['enabled']) && $container->entries->entry['parsers'][$parser_name]['enabled'] === true) {
                    if (isset($container->entries->entry['parsers'][$parser_name]['fields'])) {
                        if (is_array($container->entries->entry['parsers'][$parser_name]['fields'])) {
                            foreach ($container->entries->entry['parsers'][$parser_name]['fields'] as $field) {
                                if (! in_array($field, $container['registry']->get('flextype.settings.entries.fields'))) {
                                    if ($parser_name == 'markdown') {
                                        if (Arrays::has($container->entries->entry, $field)) {
                                            Arrays::set($container->entries->entry, $field, $container->markdown->parse(Arrays::get($container->entries->entry, $field), $cache));
                                        }
                                    }
                                    if ($parser_name == 'shortcode') {
                                        if (Arrays::has($container->entries->entry, $field)) {
                                            Arrays::set($container->entries->entry, $field, $container->shortcode->parse(Arrays::get($container->entries->entry, $field), $cache));
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
