<?php

declare(strict_types=1);

use Flextype\Flextype;
use Flextype\Parsers\Parsers;
use Flextype\Serializers\Serializers;
use Flextype\Entries\Entries;
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
use Atomastic\Arrays\Arrays;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->ensureExists(0755, true);
});

afterEach(function () {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
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

test('filterCollection helper', function () {
    $this->assertEquals([], filterCollection());
    $this->assertEquals([], filterCollection([]));
    $this->assertEquals([], filterCollection([], []));
    $this->assertEquals(['foo', 'bar'], filterCollection(['foo', 'bar'], []));

    $data = ['home'  => ['title' => 'Home'],
             'about' => ['title' => 'About'],
             'blog'  => ['title' => 'Blog']];

    // return: first
    $this->assertEquals(['title' => 'Home'], filterCollection($data, ['return' => 'first']));

    // return: last
    $this->assertEquals(['title' => 'Blog'], filterCollection($data, ['return' => 'last']));

    // return: next
    $this->assertEquals(['title' => 'About'], filterCollection($data, ['return' => 'next']));

    // return: random
    $random = filterCollection($data, ['return' => 'random']);
    $this->assertContains($random, $data);

    $random = filterCollection($data, ['return' => 'random', 'random' => 0]);
    $this->assertIsArray($random);
    $this->assertCount(0, $random);

    $random = filterCollection($data, ['return' => 'random', 'random' => 1]);
    $this->assertIsArray($random);
    $this->assertCount(1, $random);
    $this->assertContains(filterCollection($data, ['return' => 'first']), $data);

    $random = filterCollection($data, ['return' => 'random', 'random' => 2]);
    $this->assertIsArray($random);
    $this->assertCount(2, $random);
    $this->assertContains(filterCollection($random, ['return' => 'first']), $data);
    $this->assertContains(filterCollection($random, ['return' => 'last']), $data);

    // return: shuffle
    $this->assertTrue(
        is_array(filterCollection($data, ['return' => 'shuffle'])) &&
        is_array(filterCollection($data, ['return' => 'shuffle']))
    );

    // param: offset and return: all
    $this->assertEquals(['about' => ['title' => 'About'],
                         'blog'  => ['title' => 'Blog']], filterCollection($data, ['return' => 'all', 'offset' => 1]));

    // param: limit and return: all
    $this->assertEquals(['home'  => ['title' => 'Home']], filterCollection($data, ['return' => 'all', 'limit' => 1]));

    // param: sort_by and return: all
    $this->assertEquals(['about' => ['title' => 'About'],
                         'blog'  => ['title' => 'Blog'],
                         'home'  => ['title' => 'Home']],
                            filterCollection($data, ['return' => 'all',
                                                   'sort_by' => ['key' => 'title',
                                                                 'direction' => 'ASC']]));

    $this->assertEquals(['home'  => ['title' => 'Home'],
                         'blog'  => ['title' => 'Blog'],
                         'about' => ['title' => 'About']],
                            filterCollection($data, ['return' => 'all',
                                                   'sort_by' => ['key' => 'title',
                                                                 'direction' => 'DESC']]));

     $this->assertEquals(['Home' => [0 => ['title' => 'Home']],
                          'About' => [0 => ['title' => 'About']],
                          'Blog' => [0 => ['title' => 'Blog']]],
                             filterCollection($data, ['return' => 'all',
                                                    'group_by' => 'title']));

    $this->assertEquals(['home'  => ['title' => 'Home']], 
                         filterCollection($data, ['return' => 'all',
                                                  'only' => ['home']]));

    $this->assertEquals(['home'  => ['title' => 'Home']], 
                         filterCollection($data, ['return' => 'all',
                                                  'except' => ['blog', 'about']]));

    // param: where and return: all
    $this->assertEquals(['about' => ['title' => 'About']],
                        filterCollection($data, ['return' => 'all',
                                               'where' => [['key' => 'title', 'operator' => '=', 'value' => 'About']]]));
});

test('find helper', function () {
    entries()->create('foo', []);
    $this->assertTrue(find(PATH['project'] . '/entries')->hasResults());
    $this->assertTrue(find(PATH['project'] . '/entries', [])->hasResults());
    $this->assertTrue(find(PATH['project'] . '/entries', [], 'files')->hasResults());
    $this->assertTrue(find(PATH['project'], [], 'directories')->hasResults());
});
