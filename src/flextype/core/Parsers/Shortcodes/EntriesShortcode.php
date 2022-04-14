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

namespace Flextype\Parsers\Shortcodes;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Flextype\Entries\Entries;

use function arrays;
use function entries;
use function parsers;
use function registry;

// Shortcode: [entries_fetch id="entry-id" field="field-name" default="default-value"]
parsers()->shortcodes()->addHandler('entries-fetch', static function (ShortcodeInterface $s) {
    if (! registry()->get('flextype.settings.parsers.shortcodes.shortcodes.entries.enabled')) {
        return '';
    }

    $id = $s->getParameter('id');
    $options = [];

    $options['collection'] = $s->getParameter('collection') !== null ? (bool) $s->getParameter('collection') : false;
    
    if ($s->getParameter('find-depth-from') || $s->getParameter('find-depth-to')) {
        $options['find']['depth'] = [];
        $s->getParameter('find-depth-from') !== null and array_push($options['find']['depth'], $s->getParameter('find-depth-from'));
        $s->getParameter('find-depth-to') !== null and array_push($options['find']['depth'], $s->getParameter('find-depth-from'));
    }

    if ($s->getParameter('find-date-from') || $s->getParameter('find-date-to')) {
        $options['find']['date'] = [];
        $s->getParameter('find-date-from') !== null and array_push($options['find']['date'], $s->getParameter('find-date-from'));
        $s->getParameter('find-date-to') !== null and array_push($options['find']['date'], $s->getParameter('find-date-from'));
    }

    if ($s->getParameter('find-size-from') || $s->getParameter('find-size-to')) {
        $options['find']['size'] = [];
        $s->getParameter('find-size-from') !== null and array_push($options['find']['size'], $s->getParameter('find-size-from'));
        $s->getParameter('find-size-to') !== null and array_push($options['find']['size'], $s->getParameter('find-size-from'));
    }

    $s->getParameter('find-exclude') !== null and $options['find']['exclude'] = $s->getParameter('find-exclude');
    $s->getParameter('find-contains') !== null and $options['find']['contains'] = $s->getParameter('find-contains');
    $s->getParameter('find-not-contains') !== null and $options['find']['not_contains'] = $s->getParameter('find-not-contains');
    $s->getParameter('find-path') !== null and $options['find']['path'] = $s->getParameter('find-path');
    
    $s->getParameter('filter-sort-by-key') !== null and $options['filter']['sort_by']['key'] = $s->getParameter('filter-sort-by-key');
    $s->getParameter('filter-sort-by-direction') !== null and $options['filter']['sort_by']['direction'] = $s->getParameter('filter-sort-by-direction');
    $s->getParameter('filter-group-by') !== null and $options['filter']['group_by'] = $s->getParameter('filter-group-by');
    $s->getParameter('filter-return') !== null and $options['filter']['return'] = $s->getParameter('filter-return');
    $s->getParameter('filter-limit') !== null and $options['filter']['limit'] = (int) $s->getParameter('filter-limit');
    $s->getParameter('filter-offset') !== null and $options['filter']['offset'] = $s->getParameter('filter-offset');
    $s->getParameter('filter-where') !== null and $options['filter']['where'] = $s->getParameter('filter-where');

    // Re-init Entries service to avoid fields merge conflict for this new shortcode fetch query.


    //entries()->registry()->set('methods.fetch.params.id', entries()->registry()->get('methods.fetch.params.id'));
    //entries()->registry()->set('methods.fetch.params.options', entries()->registry('methods.fetch.params.options'));
    //entries()->registry()->set('methods.fetch.result', []);

    //(new Entries(registry()->get('flextype.settings.entries')))
    //dump(entries()->registry()->get('methods.fetch'));
    //dd(entries()->fetch($id, $options));

    

    //dd(entries()->setRegistry()->setOptions(registry()->get('flextype.settings.entries'))->fetch($id, $options));
    return "@type:array;" . (new Entries(registry()->get('flextype.settings.entries')))->fetch($id, $options)->toJson();
});