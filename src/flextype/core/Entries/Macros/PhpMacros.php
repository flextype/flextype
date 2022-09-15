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

namespace Flextype\Entries\Macros;

use function Flextype\registry;
use function Flextype\emitter;
use function Flextype\entries;

emitter()->addListener('onEntriesFetchSingleHasResult', static function (): void {
    if (! registry()->get('flextype.settings.entries.macros.php.enabled')) {
        return;
    }

    if (entries()->registry()->has('methods.fetch.result.macros.php')) {
        ob_start();
        eval(entries()->registry()->get('methods.fetch.result.macros.php'));
        ob_get_clean();
    }
});
