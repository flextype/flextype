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