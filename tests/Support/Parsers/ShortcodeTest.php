<?php

declare(strict_types=1);

use Thunder\Shortcode\ShortcodeFacade;
use Thunder\Shortcode\EventHandler\FilterRawEventHandler;
use Thunder\Shortcode\Events;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

test('test getInstance() method', function () {
    $this->assertInstanceOf(Flextype\Support\Parsers\Shortcode::class, flextype('parsers')->shortcode()->getInstance());
});

test('test addHandler() method', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, flextype('parsers')->shortcode()->addHandler('foo', static function() { return ''; }));
});

test('test addEventHandler() method', function () {
    flextype('parsers')->shortcode()->addHandler('barz', static function () {
        return 'Barz';
    });
    flextype('parsers')->shortcode()->addEventHandler(Events::FILTER_SHORTCODES, new FilterRawEventHandler(['barz']));
    $this->assertEquals('Barz', flextype('parsers')->shortcode()->process('[barz]'));
});

test('test parse() method', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, flextype('parsers')->shortcode()->addHandler('bar', static function() { return ''; }));
    $this->assertTrue(is_array(flextype('parsers')->shortcode()->parse('[bar]')));
    $this->assertTrue(is_object(flextype('parsers')->shortcode()->parse('[bar]')[0]));
});

test('test process() method', function () {
    $this->assertInstanceOf(Thunder\Shortcode\ShortcodeFacade::class, flextype('parsers')->shortcode()->addHandler('zed', static function() { return 'Zed'; }));
    $this->assertEquals('Zed', flextype('parsers')->shortcode()->process('[zed]'));
    $this->assertEquals('fòôBàřZed', flextype('parsers')->shortcode()->process('fòôBàř[zed]'));
});

test('test getCacheID() method', function () {
    $this->assertNotEquals(flextype('parsers')->shortcode()->getCacheID('fòôBàř[bar]'),
                           flextype('parsers')->shortcode()->getCacheID('fòôBàř[foo]'));
});
