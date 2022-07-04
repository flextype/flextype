<?php

declare(strict_types=1);

use Flextype\Flextype;
use Flextype\Parsers\Parsers;
use Flextype\Serializers\Serializers;
use Flextype\Entries\Entries;
use Flextype\Console\FlextypeConsole;
use Glowy\Strings\Strings;
use Glowy\Registry\Registry;
use Glowy\Session\Session;
use Slim\App;
use DI\Container;
use League\Event\Emitter;
use Phpfastcache\Helper\Psr16Adapter as Cache;
use Monolog\Logger;
use Glowy\Csrf\Csrf;
use Cocur\Slugify\Slugify;
use Glowy\Arrays\Arrays;
use function Glowy\Filesystem\filesystem;
use function Glowy\Strings\strings;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('flextype helper', function () {
    $this->assertSame(flextype(), Flextype::getInstance());
    expect(flextype())->toBeInstanceOf(Flextype::class);
});

test('app helper', function () {
    $this->assertSame(app(), Flextype::getInstance()->app());
    expect(app())->toBeInstanceOf(App::class);
});

test('container helper', function () {
    $this->assertSame(container(), Flextype::getInstance()->container());
    $this->assertSame(container(), Flextype::getInstance()->app()->getContainer());
    expect(container())->toBeInstanceOf(Container::class);
});

test('emitter helper', function () {
    $this->assertSame(emitter(), Flextype::getInstance()->container()->get('emitter'));
    expect(emitter())->toBeInstanceOf(Emitter::class);
});

test('registry helper', function () {
    $this->assertSame(registry(), Flextype::getInstance()->container()->get('registry'));
    expect(registry())->toBeInstanceOf(Registry::class);
});

test('session helper', function () {
    $this->assertSame(session(), Flextype::getInstance()->container()->get('session'));
    expect(session())->toBeInstanceOf(Session::class);
});

test('console helper', function () {
    $this->assertSame(console(), Flextype::getInstance()->container()->get('console'));
    expect(console())->toBeInstanceOf(FlextypeConsole::class);
});

test('cache helper', function () {
    $this->assertSame(cache(), Flextype::getInstance()->container()->get('cache'));
    expect(cache())->toBeInstanceOf(Cache::class);
});

test('entries helper', function () {
    $this->assertSame(entries(), Flextype::getInstance()->container()->get('entries'));
    expect(entries())->toBeInstanceOf(Entries::class);
});

test('logger helper', function () {
    $this->assertSame(logger(), Flextype::getInstance()->container()->get('logger'));
    expect(logger())->toBeInstanceOf(Logger::class);
});

test('parsers helper', function () {
    $this->assertSame(parsers(), Flextype::getInstance()->container()->get('parsers'));
    expect(parsers())->toBeInstanceOf(Parsers::class);
});

test('serializers helper', function () {
    $this->assertSame(serializers(), Flextype::getInstance()->container()->get('serializers'));
    expect(serializers())->toBeInstanceOf(Serializers::class);
});

test('csrf helper', function () {
    $this->assertSame(csrf(), Flextype::getInstance()->container()->get('csrf'));
    expect(csrf())->toBeInstanceOf(Csrf::class);
});

test('slugify helper', function () {
    $this->assertSame(slugify(), Flextype::getInstance()->container()->get('slugify'));
    expect(slugify())->toBeInstanceOf(Slugify::class);
});

test('find helper', function () {
    entries()->create('foo', []);
    $this->assertTrue(find(FLEXTYPE_PATH_PROJECT . '/entries')->hasResults());
    $this->assertTrue(find(FLEXTYPE_PATH_PROJECT . '/entries', [])->hasResults());
    $this->assertTrue(find(FLEXTYPE_PATH_PROJECT . '/entries', [], 'files')->hasResults());
    $this->assertTrue(find(FLEXTYPE_PATH_PROJECT, [], 'directories')->hasResults());
});

test('generateToken helper', function () {
    $this->assertTrue(strings(generateToken())->length() == 32);
});

test('generateTokenHash helper', function () {
    $this->assertTrue(generateTokenHash(generateToken()) != generateTokenHash(generateToken()));
});

test('verifyTokenHash helper', function () {
    $token = generateToken();
    $tokenHashed = generateTokenHash($token);
    $this->assertTrue(verifyTokenHash($token, $tokenHashed));
});