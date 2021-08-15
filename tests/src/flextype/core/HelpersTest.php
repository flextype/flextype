<?php

declare(strict_types=1);

use Flextype\Flextype;
use Flextype\Content\Content;
use Flextype\Media\Media;
use Flextype\Parsers\Parsers;
use Flextype\Serializers\Serializers;
use Flextype\Tokens\Tokens;
use Atomastic\Strings\Strings;
use Atomastic\Registry\Registry;
use Atomastic\Session\Session;
use Slim\App;
use DI\Container;
use League\Event\Emitter;
use Phpfastcache\Helper\Psr16Adapter as Cache;
use Monolog\Logger;
use Atomastic\Csrf\Csrf;
use Cocur\Slugify\Slugify;


test('test flextype() helper', function () {
    $this->assertSame(flextype(), Flextype::getInstance());
    expect(flextype())->toBeInstanceOf(Flextype::class);
});

test('test app() helper', function () {
    $this->assertSame(app(), Flextype::getInstance()->app());
    expect(app())->toBeInstanceOf(App::class);
});

test('test container() helper', function () {
    $this->assertSame(container(), Flextype::getInstance()->container());
    $this->assertSame(container(), Flextype::getInstance()->app()->getContainer());
    expect(container())->toBeInstanceOf(Container::class);
});

test('test emitter() helper', function () {
    $this->assertSame(emitter(), Flextype::getInstance()->container()->get('emitter'));
    expect(emitter())->toBeInstanceOf(Emitter::class);
});

test('test registry() helper', function () {
    $this->assertSame(registry(), Flextype::getInstance()->container()->get('registry'));
    expect(registry())->toBeInstanceOf(Registry::class);
});

test('test session() helper', function () {
    $this->assertSame(session(), Flextype::getInstance()->container()->get('session'));
    expect(session())->toBeInstanceOf(Session::class);
});

test('test cache() helper', function () {
    $this->assertSame(cache(), Flextype::getInstance()->container()->get('cache'));
    expect(cache())->toBeInstanceOf(Cache::class);
});

test('test media() helper', function () {
    $this->assertSame(media(), Flextype::getInstance()->container()->get('media'));
    expect(media())->toBeInstanceOf(Media::class);
});

test('test content() helper', function () {
    $this->assertSame(content(), Flextype::getInstance()->container()->get('content'));
    expect(content())->toBeInstanceOf(Content::class);
});

test('test logger() helper', function () {
    $this->assertSame(logger(), Flextype::getInstance()->container()->get('logger'));
    expect(logger())->toBeInstanceOf(Logger::class);
});

test('test parsers() helper', function () {
    $this->assertSame(parsers(), Flextype::getInstance()->container()->get('parsers'));
    expect(parsers())->toBeInstanceOf(Parsers::class);
});

test('test serializers() helper', function () {
    $this->assertSame(serializers(), Flextype::getInstance()->container()->get('serializers'));
    expect(serializers())->toBeInstanceOf(Serializers::class);
});

test('test csrf() helper', function () {
    $this->assertSame(csrf(), Flextype::getInstance()->container()->get('csrf'));
    expect(csrf())->toBeInstanceOf(Csrf::class);
});

test('test slugify() helper', function () {
    $this->assertSame(slugify(), Flextype::getInstance()->container()->get('slugify'));
    expect(slugify())->toBeInstanceOf(Slugify::class);
});