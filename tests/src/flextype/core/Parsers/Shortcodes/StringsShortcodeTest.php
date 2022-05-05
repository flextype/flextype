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

    // base64Encode
    $this->assertEquals("dGVzdA==", parsers()->shortcodes()->parse('[strings base64Encode]test[/strings]'));

    // between
    $this->assertEquals("b", parsers()->shortcodes()->parse('[strings between=a|c]abc[/strings]'));

    // camel
    $this->assertEquals("fooBar", parsers()->shortcodes()->parse('[strings camel]foo_bar[/strings]'));

    // capitalize
    $this->assertEquals("That Country Was At The Same Stage Of Development As The United States In The 1940S", parsers()->shortcodes()->parse('[strings capitalize]that country was at the same stage of development as the United States in the 1940s[/strings]'));

    // chars
    $this->assertEquals('["c","a","r","_","f","ò","ô","_","b","à","ř","s","_","a","p","p","l","e"]', parsers()->shortcodes()->parse('[strings chars]car_fòô_bàřs_apple[/strings]'));

    // charsFrequency
    $this->assertEquals('{"_":"16.67","a":"11.11","p":"11.11","c":"5.56","r":"5.56","f":"5.56","ò":"5.56","ô":"5.56","b":"5.56","à":"5.56","ř":"5.56","s":"5.56","l":"5.56","e":"5.56"}', parsers()->shortcodes()->parse('[strings charsFrequency]car_fòô_bàřs_apple[/strings]'));

    // contains
    $this->assertEquals("true", parsers()->shortcodes()->parse('[strings contains=SG-1]SG-1 returns from an off-world mission to P9Y-3C3[/strings]'));
    $this->assertEquals("false", parsers()->shortcodes()->parse('[strings contains=sg-1|false]SG-1 returns from an off-world mission to P9Y-3C3[/strings]'));

});