<?php

declare(strict_types=1);

use Flextype\Flextype;
use Flextype\Content\Content;
use Glowy\Strings\Strings;

test('get flextype version', function () {
    $this->assertTrue(!Strings::create(Flextype::getInstance()->getVersion())->isEmpty());
});

test('get flextype instance', function () {
    $firstCall = Flextype::getInstance();
    $secondCall = Flextype::getInstance();

    $this->assertInstanceOf(Flextype::class, $firstCall);
    $this->assertSame($firstCall, $secondCall);
});