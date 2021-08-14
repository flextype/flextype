<?php

declare(strict_types=1);

use Flextype\Flextype;
use Flextype\Content\Content;
use Atomastic\Strings\Strings;
use Slim\App;

test('test flextype() helper', function () {
    $this->assertSame(flextype(), Flextype::getInstance());
});

test('test app() helper', function () {
    $this->assertSame(app(), Flextype::getInstance()->app());
});

test('test container() helper', function () {
    $this->assertSame(container(), Flextype::getInstance()->container());
});

test('test emitter() helper', function () {
    $this->assertSame(emitter(), Flextype::getInstance()->container()->get('emitter'));
});