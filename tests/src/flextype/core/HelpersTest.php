<?php

declare(strict_types=1);

use Flextype\Flextype;
use Flextype\Content\Content;
use Atomastic\Strings\Strings;
use Slim\App;
use DI\Container;

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
});