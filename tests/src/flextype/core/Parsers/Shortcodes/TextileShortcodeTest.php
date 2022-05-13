<?php

declare(strict_types=1);

test('[textile] shortcode', function () {
    $this->assertEquals("<p><b>Foo</b></p>",
                        parsers()->shortcodes()->parse('[textile]**Foo**[/textile]'));
});
