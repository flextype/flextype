<?php

declare(strict_types=1);

use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\EventHandler\FilterRawEventHandler;
use Thunder\Shortcode\Events;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

test('get shortcodes instance', function () {
    $this->assertInstanceOf(Flextype\Parsers\Shortcodes::class, parsers()->shortcodes()->getInstance());
});

test('add shortcodes handler', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, parsers()->shortcodes()->addHandler('foo', static function() { return ''; }));
});

test('add shortcodes event handler', function () {
    parsers()->shortcodes()->addHandler('barz', static function () {
        return 'Barz';
    });
    parsers()->shortcodes()->addEventHandler(Events::FILTER_SHORTCODES, new FilterRawEventHandler(['barz']));
    $this->assertEquals('Barz', parsers()->shortcodes()->parse('(barz)'));
});

test('parse text', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, parsers()->shortcodes()->addHandler('bar', static function() { return ''; }));
    $this->assertTrue(is_array(parsers()->shortcodes()->parseText('(bar)')));
    $this->assertTrue(is_object(parsers()->shortcodes()->parseText('(bar)')[0]));
});

test('parse shortcodes', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, parsers()->shortcodes()->addHandler('zed', static function() { return 'Zed'; }));
    $this->assertEquals('Zed', parsers()->shortcodes()->parse('(zed)'));
    $this->assertEquals('fòôBàřZed', parsers()->shortcodes()->parse('fòôBàř(zed)'));
});

test('get cache ID', function () {
    $this->assertNotEquals(parsers()->shortcodes()->getCacheID('fòôBàř(bar)'),
                           parsers()->shortcodes()->getCacheID('fòôBàř(foo)'));
});
