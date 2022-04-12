<?php

declare(strict_types=1);

test('[php] shortcode', function () {
    $this->assertEquals("Foo",
                        parsers()->shortcodes()->parse('[php]echo "Foo";[/php]'));
});
