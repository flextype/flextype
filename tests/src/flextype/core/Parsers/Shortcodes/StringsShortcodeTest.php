<?php

declare(strict_types=1);

test('[strings] shortcode', function () {    
    // lower
    $this->assertEquals("zed foo bar", parsers()->shortcodes()->parse('[strings lower]zed foo bar[/strings]'));

    // upper
    $this->assertEquals("ZED FOO BAR", parsers()->shortcodes()->parse('[strings upper]zed foo bar[/strings]'));

    // append
    $this->assertEquals("zed foo bar", parsers()->shortcodes()->parse('[strings append=" bar"]zed foo[/strings]'));

    // prepend
    $this->assertEquals("zed foo bar", parsers()->shortcodes()->parse('[strings prepend="zed "]foo bar[/strings]'));

    // sort
    $this->assertEquals("a b c", parsers()->shortcodes()->parse('[strings sort="asc"]b a c[/strings]'));
    $this->assertEquals("c b a", parsers()->shortcodes()->parse('[strings sort="desc"]b a c[/strings]'));

    // after
    $this->assertEquals(" bar zed", parsers()->shortcodes()->parse('[strings after="foo"]foo bar zed[/strings]'));

    // afterLast
    $this->assertEquals(" bar zed", parsers()->shortcodes()->parse('[strings afterLast="foo"]foo foo bar zed[/strings]'));

    // before
    $this->assertEquals("foo bar ", parsers()->shortcodes()->parse('[strings before="zed"]foo bar zed[/strings]'));

    // beforeLast
    $this->assertEquals("foo ", parsers()->shortcodes()->parse('[strings beforeLast="foo"]foo foo bar zed[/strings]'));

    // wordsLimit
    $this->assertEquals("foo...", parsers()->shortcodes()->parse('[strings wordsLimit="1"]foo bar zed[/strings]'));
    $this->assertEquals("foo >>>", parsers()->shortcodes()->parse('[strings wordsLimit="1| >>>"]foo bar zed[/strings]'));

    // at
    $this->assertEquals("a", parsers()->shortcodes()->parse('[strings at=0]abc[/strings]'));
    $this->assertEquals("b", parsers()->shortcodes()->parse('[strings at=1]abc[/strings]'));
    $this->assertEquals("c", parsers()->shortcodes()->parse('[strings at=2]abc[/strings]'));

    // base64Decode
    $this->assertEquals("test", parsers()->shortcodes()->parse('[strings base64Decode]dGVzdA==[/strings]'));
});