<?php

declare(strict_types=1);

use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\EventHandler\FilterRawEventHandler;
use Thunder\Shortcode\Events;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

test('test getInstance() method', function () {
    $this->assertInstanceOf(Flextype\Parsers\Shortcodes::class, parsers()->shortcodes()->getInstance());
});

test('test addHandler() method', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, parsers()->shortcodes()->addHandler('foo', static function() { return ''; }));
});

test('test addEventHandler() method', function () {
    parsers()->shortcodes()->addHandler('barz', static function () {
        return 'Barz';
    });
    parsers()->shortcodes()->addEventHandler(Events::FILTER_SHORTCODES, new FilterRawEventHandler(['barz']));
    $this->assertEquals('Barz', parsers()->shortcodes()->parse('[barz]'));
});

test('test parseText() method', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, parsers()->shortcodes()->addHandler('bar', static function() { return ''; }));
    $this->assertTrue(is_array(parsers()->shortcodes()->parseText('[bar]')));
    $this->assertTrue(is_object(parsers()->shortcodes()->parseText('[bar]')[0]));
});

test('test parse() method', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, parsers()->shortcodes()->addHandler('zed', static function() { return 'Zed'; }));
    $this->assertEquals('Zed', parsers()->shortcodes()->parse('[zed]'));
    $this->assertEquals('fòôBàřZed', parsers()->shortcodes()->parse('fòôBàř[zed]'));
});

test('test getCacheID() method', function () {
    $this->assertNotEquals(parsers()->shortcodes()->getCacheID('fòôBàř[bar]'),
                           parsers()->shortcodes()->getCacheID('fòôBàř[foo]'));
});
