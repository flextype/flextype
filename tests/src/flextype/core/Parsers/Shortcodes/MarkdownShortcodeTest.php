<?php

declare(strict_types=1);

test('[markdown] shortcode', function () {
    $this->assertEquals("<p><strong>Foo</strong></p>\n",
                        parsers()->shortcodes()->parse('(markdown)**Foo**(/markdown)'));
});
