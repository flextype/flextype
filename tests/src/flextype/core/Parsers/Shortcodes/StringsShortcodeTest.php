<?php

declare(strict_types=1);

test('strings shortcode', function () {

    // lower
    $this->assertEquals("zed foo bar", parsers()->shortcodes()->parse("(strings lower)zed foo bar(/strings)"));

    // upper
    $this->assertEquals("ZED FOO BAR", parsers()->shortcodes()->parse("(strings upper)zed foo bar(/strings)"));

    // append
    $this->assertEquals("zed foo bar", parsers()->shortcodes()->parse("(strings append:' bar')zed foo(/strings)"));
    $this->assertEquals("zed foo bar zed", parsers()->shortcodes()->parse("(strings append:' bar, zed')zed foo(/strings)"));

    // prepend
    $this->assertEquals("zed foo bar", parsers()->shortcodes()->parse("(strings prepend:'zed ')foo bar(/strings)"));

    // sort
    $this->assertEquals("a b c", parsers()->shortcodes()->parse("(strings sort:'asc')b a c(/strings)"));
    $this->assertEquals("c b a", parsers()->shortcodes()->parse("(strings sort:'desc')b a c(/strings)"));

    // after
    $this->assertEquals(" bar zed", parsers()->shortcodes()->parse("(strings after:'foo')foo bar zed(/strings)"));

    // afterLast
    $this->assertEquals(" bar zed", parsers()->shortcodes()->parse("(strings afterLast:'foo')foo foo bar zed(/strings)"));

    // before
    $this->assertEquals("foo bar ", parsers()->shortcodes()->parse("(strings before:'zed')foo bar zed(/strings)"));

    // beforeLast
    $this->assertEquals("foo ", parsers()->shortcodes()->parse("(strings beforeLast:'foo')foo foo bar zed(/strings)"));

    // wordsLimit
    $this->assertEquals("foo...", parsers()->shortcodes()->parse("(strings wordsLimit:'1')foo bar zed(/strings)"));
    $this->assertEquals("foo >>>", parsers()->shortcodes()->parse("(strings wordsLimit:'1, >>>')foo bar zed(/strings)"));

    // at
    $this->assertEquals("a", parsers()->shortcodes()->parse("(strings at:0)abc(/strings)"));
    $this->assertEquals("b", parsers()->shortcodes()->parse("(strings at:1)abc(/strings)"));
    $this->assertEquals("c", parsers()->shortcodes()->parse("(strings at:2)abc(/strings)"));

    // base64Decode
    $this->assertEquals("test", parsers()->shortcodes()->parse("(strings base64Decode)dGVzdA==(/strings)"));

    // base64Encode
    $this->assertEquals("dGVzdA==", parsers()->shortcodes()->parse("(strings base64Encode)test(/strings)"));

    // between
    $this->assertEquals("b", parsers()->shortcodes()->parse("(strings between:a,c)abc(/strings)"));

    // camel
    $this->assertEquals("fooBar", parsers()->shortcodes()->parse("(strings camel)foo_bar(/strings)"));

    // capitalize
    $this->assertEquals("That Country Was At The Same Stage Of Development As The United States In The 1940S", parsers()->shortcodes()->parse("(strings capitalize)that country was at the same stage of development as the United States in the 1940s(/strings)"));

    // chars
    $this->assertEquals('["c","a","r","_","f","ò","ô","_","b","à","ř","s","_","a","p","p","l","e"]', parsers()->shortcodes()->parse("(strings chars)car_fòô_bàřs_apple(/strings)"));

    // charsFrequency
    $this->assertEquals('{"_":"16.67","a":"11.11","p":"11.11","c":"5.56","r":"5.56","f":"5.56","ò":"5.56","ô":"5.56","b":"5.56","à":"5.56","ř":"5.56","s":"5.56","l":"5.56","e":"5.56"}', parsers()->shortcodes()->parse("(strings charsFrequency)car_fòô_bàřs_apple(/strings)"));

    // contains
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings contains:SG-1)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings contains:SG-1,P9Y-3C3)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));
    $this->assertEquals("false", parsers()->shortcodes()->parse("(strings contains:'')SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));
    $this->assertEquals("false", parsers()->shortcodes()->parse("(strings contains:sg-1)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));

    // containsAll
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings containsAll:SG-1)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings containsAll:SG-1&P9Y-3C3)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));
    $this->assertEquals("false", parsers()->shortcodes()->parse("(strings containsAll:SG-1&XXX-3C3)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings containsAll:sg-1&P9Y-3C3,false)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));

    // containsAny
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings containsAny:SG-1)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings containsAny:SG-1&P9Y-3C3)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings containsAny:SG-1&XXX-3C3)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings containsAny:sg-1&P9Y-3C3,false)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));

    // count
    $this->assertEquals(49, parsers()->shortcodes()->parse("(strings count)SG-1 returns from an off-world mission to P9Y-3C3(/strings)"));

    // countSubString
    $this->assertEquals(1, parsers()->shortcodes()->parse("(strings countSubString:test)Test string here for test(/strings)"));
    $this->assertEquals(2, parsers()->shortcodes()->parse("(strings countSubString:test,false)Test string here for test(/strings)"));

    // crc32
    $this->assertEquals(3632233996, parsers()->shortcodes()->parse("(strings crc32)test(/strings)"));

    // endsWith
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings endsWith:'/')/movies/sg-1/season-5/episode-21/(/strings)"));
    $this->assertEquals("false", parsers()->shortcodes()->parse("(strings endsWith:'/')/movies/sg-1/season-5/episode-21(/strings)"));

    // finish
    $this->assertEquals("/movies/sg-1/season-5/episode-21/", parsers()->shortcodes()->parse("(strings finish:'/')/movies/sg-1/season-5/episode-21(/strings)"));

    // firstSegment
    $this->assertEquals("SG-1", parsers()->shortcodes()->parse("(strings firstSegment)SG-1 returns from an off-world mission(/strings)"));
    $this->assertEquals("SG", parsers()->shortcodes()->parse("(strings firstSegment:'-')SG-1 returns from an off-world mission(/strings)"));

    // format
    $this->assertEquals("There are 5 monkeys in the tree", parsers()->shortcodes()->parse("(strings format:'5,tree')There are %d monkeys in the %s(/strings)"));

    // getEncoding
    $this->assertEquals("UTF-8", parsers()->shortcodes()->parse("(strings getEncoding)Foo(/strings)"));

    // hash
    $this->assertEquals("1356c67d7ad1638d816bfb822dd2c25d", parsers()->shortcodes()->parse("(strings hash)Foo(/strings)"));
    $this->assertEquals("201a6b3053cc1422d2c3670b62616221d2290929", parsers()->shortcodes()->parse("(strings hash:sha1)Foo(/strings)"));

    // increment
    $this->assertEquals("Page_2", parsers()->shortcodes()->parse("(strings increment)Page_1(/strings)"));
    $this->assertEquals("Page-2", parsers()->shortcodes()->parse("(strings increment:1,-)Page-1(/strings)"));

    // indexOf
    $this->assertEquals(1, parsers()->shortcodes()->parse("(strings indexOf:e)hello(/strings)"));

    // indexOfLast
    $this->assertEquals(3, parsers()->shortcodes()->parse("(strings indexOfLast:l)hello(/strings)"));

    // insert
    $this->assertEquals("hello world", parsers()->shortcodes()->parse("(strings insert:'hello ,0')world(/strings)"));
    $this->assertEquals("hello world", parsers()->shortcodes()->parse("(strings insert:' world,5')hello(/strings)"));

    // isAlpha
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isAlpha)foo(/strings)"));

    // isAlphanumeric
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isAlphanumeric)fòôbàřs12345(/strings)"));

    // isAscii
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isAscii)#@$%(/strings)"));

    // isBase64
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isBlank)(/strings)"));

    // isDigit
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isDigit)01234569(/strings)"));

    // isEmail
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isEmail)awilum@msn.com(/strings)"));

    // isEmpty
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isEmpty)(/strings)"));

    // isEqual
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isEqual:Foo)Foo(/strings)"));

    // isTrue
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isTrue)true(/strings)"));

    // isFalse
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isFalse)false(/strings)"));

    // isHexadecimal
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isHexadecimal)19FDE(/strings)"));

    // isHTML
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isHTML)<p>Hello</p>(/strings)"));

    // isIP
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isIP)127.0.0.1(/strings)"));

    // isJSON
    $this->assertEquals("true", parsers()->shortcodes()->parse('(strings isJson){"foo":"bar"}(/strings)'));

    // isLower
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isLower)foo(/strings)"));

    // isMAC
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isMAC)00:00:00:00:00:00(/strings)"));

    // isUpper 
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isUpper)FOO(/strings)"));

    // isNumeric    
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isNumeric)12345(/strings)"));

    // isPrintable 
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isPrintable)!@#$%^&*()_+-:(){};\':\./<>?\\`~(/strings)"));

    // isPunctuation
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isPunctuation),(/strings)"));

    // isUrl    
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isUrl)http://awilum.github.io(/strings)"));
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isUrl)https://awilum.github.io(/strings)"));
    
    // isSimilar
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isSimilar:Foo)Foo(/strings)"));
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings isSimilar:Foo,50)Foo(/strings)"));

    // isSerialized
    $this->assertEquals("true", parsers()->shortcodes()->parse('(strings isSerialized)a:1:{i:0;s:3:"foo";}(/strings)'));

    // kebab
    $this->assertEquals("foo-bar", parsers()->shortcodes()->parse("(strings kebab)Foo Bar(/strings)"));

    // lastSegment
    $this->assertEquals("baz", parsers()->shortcodes()->parse("(strings lastSegment)foo bar baz(/strings)"));
    $this->assertEquals("baz", parsers()->shortcodes()->parse("(strings lastSegment:'/')foo/bar/baz(/strings)"));
    
    // length
    $this->assertEquals(11, parsers()->shortcodes()->parse("(strings length)foo bar baz(/strings)"));

    // lines
    $this->assertEquals('["Fòô òô"," fòô fò fò ","fò"]', parsers()->shortcodes()->parse("(strings lines)Fòô òô\n fòô fò fò \nfò\r(/strings)"));

    // md5
    $this->assertEquals("01677e4c0ae5468b9b8b823487f14524", parsers()->shortcodes()->parse("(strings md5)Foo Bar(/strings)"));

    // move
    $this->assertEquals("worldhello", parsers()->shortcodes()->parse("(strings move:0,5,10)helloworld(/strings)"));

    // normalizeNewLines
    $this->assertEquals("\n \n", parsers()->shortcodes()->parse("(strings normalizeNewLines)\r\n \r(/strings)"));

    // normalizeSpaces
    $this->assertEquals("foo bar baz", parsers()->shortcodes()->parse("(strings normalizeSpaces)foo  bar  baz(/strings)"));

    // offsetExists
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings offsetExists:0)foo bar baz(/strings)"));

    // offsetGet
    $this->assertEquals("f", parsers()->shortcodes()->parse("(strings offsetGet:0)foo bar baz(/strings)"));

    // padBoth
    $this->assertEquals("   foo   ", parsers()->shortcodes()->parse("(strings padBoth:9)foo(/strings)"));

    // padLeft
    $this->assertEquals("   foo", parsers()->shortcodes()->parse("(strings padLeft:6)foo(/strings)"));

    // padRight
    $this->assertEquals("foo   ", parsers()->shortcodes()->parse("(strings padRight:6)foo(/strings)"));

    // quotesToEntities
    $this->assertEquals("&quot;foo&quot;", parsers()->shortcodes()->parse('(strings quotesToEntities)"foo"(/strings)'));

    // random
    $test1 = parsers()->shortcodes()->parse("(strings random /)");
    $test2 = parsers()->shortcodes()->parse("(strings random:10 /)");
    $test3 = parsers()->shortcodes()->parse("(strings random:4,1234 /)");
    $this->assertEquals(64, strings($test1)->length());
    $this->assertEquals(10, strings($test2)->length());
    $this->assertEquals(4, strings($test3)->length());

    // reduceSlashes
    $this->assertEquals("foo/bar/baz", parsers()->shortcodes()->parse("(strings reduceSlashes)foo//bar/baz(/strings)"));

    // repeat
    $this->assertEquals("foofoofoo", parsers()->shortcodes()->parse("(strings repeat:3)foo(/strings)"));

    // replace
    $this->assertEquals("bar baz", parsers()->shortcodes()->parse("(strings replace:foo,bar)foo baz(/strings)"));

    // replaceDashes
    $this->assertEquals("foobarbaz", parsers()->shortcodes()->parse("(strings replaceDashes)foo-bar-baz(/strings)"));
    $this->assertEquals("foo_bar_baz", parsers()->shortcodes()->parse("(strings replaceDashes:'_')foo-bar-baz(/strings)"));

    // replaceFirst
    $this->assertEquals("bar foo bar", parsers()->shortcodes()->parse("(strings replaceFirst:foo,bar)foo foo bar(/strings)"));

    // replaceLast
    $this->assertEquals("foo bar bar", parsers()->shortcodes()->parse("(strings replaceLast:foo,bar)foo foo bar(/strings)"));

    // replaceNonAlpha
    $this->assertEquals("foo  baz  bar", parsers()->shortcodes()->parse("(strings replaceNonAlpha)foo 123 baz 345 bar(/strings)"));

    // replaceNonAlphanumeric
    $this->assertEquals("Fòôbàřs123", parsers()->shortcodes()->parse("(strings replaceNonAlphanumeric)Fòô-bàřs-123(/strings)"));

    // replacePunctuations
    $this->assertEquals("foo 123 baz 345 bar", parsers()->shortcodes()->parse("(strings replacePunctuations)foo 123, baz, 345 bar(/strings)"));

    // reverse
    $this->assertEquals("oof", parsers()->shortcodes()->parse("(strings reverse)foo(/strings)"));

    // segement
    $this->assertEquals("foo", parsers()->shortcodes()->parse("(strings segment:0)foo bar baz(/strings)"));
    $this->assertEquals("foo", parsers()->shortcodes()->parse("(strings segment:'0,/')foo/bar/baz(/strings)"));

    // segements
    $this->assertEquals('["foo","bar","baz"]', parsers()->shortcodes()->parse("(strings segments)foo bar baz(/strings)"));

    // sha1
    $this->assertEquals("5cb8681884af2923487a6034d8dbe753828e2660", parsers()->shortcodes()->parse("(strings sha1)Foo Bar(/strings)"));

    // sha256
    $this->assertEquals("55282c18206b9beb9998f5eaa15b85c9388463965678af5209e2cc3a3ff5b947", parsers()->shortcodes()->parse("(strings sha256)Foo Bar(/strings)"));

    // shuffle
    $this->assertEquals(9, strings(parsers()->shortcodes()->parse("(strings shuffle)123456890(/strings)"))->length());

    // similarity
    $this->assertEquals(100, parsers()->shortcodes()->parse("(strings similarity:foo)foo(/strings)"));

    // snake    
    $this->assertEquals("foo_bar", parsers()->shortcodes()->parse("(strings snake)fooBar(/strings)"));

    // start
    $this->assertEquals("/movies/sg-1/season-5/episode-21/", parsers()->shortcodes()->parse("(strings start:'/')movies/sg-1/season-5/episode-21/(/strings)"));

    // startsWith
    $this->assertEquals("true", parsers()->shortcodes()->parse("(strings startsWith:'/')/foo/(/strings)"));

    // stripQuotes
    $this->assertEquals("some text here", parsers()->shortcodes()->parse('(strings stripQuotes)some "text" here(/strings)'));

    // stripSpaces
    $this->assertEquals("foobarbaz", parsers()->shortcodes()->parse("(strings stripSpaces)foo bar baz(/strings)"));

    // studly   
    $this->assertEquals("FooBar", parsers()->shortcodes()->parse("(strings studly)foo_bar(/strings)"));

    // substr
    $this->assertEquals("bar baz", parsers()->shortcodes()->parse("(strings substr:4)foo bar baz(/strings)"));

    // trim
    $this->assertEquals("foo bar baz", parsers()->shortcodes()->parse("(strings trim) foo bar baz (/strings)"));

    // trimLeft
    $this->assertEquals("foo bar baz", parsers()->shortcodes()->parse("(strings trimLeft) foo bar baz(/strings)"));

    // trimRight
    $this->assertEquals("foo bar baz", parsers()->shortcodes()->parse("(strings trimRight)foo bar baz (/strings)"));

    // trimSlashes
    $this->assertEquals("foo/bar/baz", parsers()->shortcodes()->parse("(strings trimSlashes)/foo/bar/baz/(/strings)"));

    // ucfirst
    $this->assertEquals("Foo", parsers()->shortcodes()->parse("(strings ucfirst)foo(/strings)"));

    // wordsCount
    $this->assertEquals(3, parsers()->shortcodes()->parse("(strings wordsCount)foo bar baz(/strings)"));

    // words
    $this->assertEquals('["foo","bar","baz"]', parsers()->shortcodes()->parse("(strings words)foo bar baz(/strings)"));

    // wordsFrequency
    $this->assertEquals('{"foo":"33.33","bar":"33.33","baz":"33.33"}', parsers()->shortcodes()->parse("(strings wordsFrequency)foo bar baz(/strings)"));
});

test('strings nested shortcode', function () {
    expect(parsers()->shortcodes()->parse("(strings append:'(strings hash)(strings upper)foo(/strings)(/strings)')Hash: (/strings)"))->toBe('Hash: 901890a8e9c8cf6d5a1a542b229febff');
});

test('strings shortcode disabled', function () {
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.strings.enabled', false);
    expect(parsers()->shortcodes()->parse("(strings ucfirst)foo(/strings)"))->toBe('');
    registry()->set('flextype.settings.parsers.shortcodes.shortcodes.strings.enabled', true);
});