<?php

declare(strict_types=1);

use Flextype\Flextype;
use Flextype\Entries\Entries;
use Atomastic\Strings\Strings;

test('test getVersion() method', function () {
    $this->assertTrue(!Strings::create(flextype()->getVersion())->isEmpty());
});

test('test getInstance() method', function () {
    $firstCall = flextype();
    $secondCall = Flextype::getInstance();

    $this->assertInstanceOf(Flextype::class, $firstCall);
    $this->assertSame($firstCall, $secondCall);
});