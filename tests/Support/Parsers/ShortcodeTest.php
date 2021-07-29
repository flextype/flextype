<?php

declare(strict_types=1);

use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\EventHandler\FilterRawEventHandler;
use Thunder\Shortcode\Events;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

test('test getInstance() method', function () {
    $this->assertInstanceOf(Flextype\Support\Parsers\Shortcode::class, parsers()->shortcode()->getInstance());
});

test('test addHandler() method', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, parsers()->shortcode()->addHandler('foo', static function() { return ''; }));
});

test('test addEventHandler() method', function () {
    parsers()->shortcode()->addHandler('barz', static function () {
        return 'Barz';
    });
    parsers()->shortcode()->addEventHandler(Events::FILTER_SHORTCODES, new FilterRawEventHandler(['barz']));
    $this->assertEquals('Barz', parsers()->shortcode()->process('[barz]'));
});

test('test parse() method', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, parsers()->shortcode()->addHandler('bar', static function() { return ''; }));
    $this->assertTrue(is_array(parsers()->shortcode()->parse('[bar]')));
    $this->assertTrue(is_object(parsers()->shortcode()->parse('[bar]')[0]));
});

test('test process() method', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, parsers()->shortcode()->addHandler('zed', static function() { return 'Zed'; }));
    $this->assertEquals('Zed', parsers()->shortcode()->process('[zed]'));
    $this->assertEquals('fòôBàřZed', parsers()->shortcode()->process('fòôBàř[zed]'));
});

test('test getCacheID() method', function () {
    $this->assertNotEquals(parsers()->shortcode()->getCacheID('fòôBàř[bar]'),
                           parsers()->shortcode()->getCacheID('fòôBàř[foo]'));
});
