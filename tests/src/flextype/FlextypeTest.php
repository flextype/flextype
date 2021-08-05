<?php

declare(strict_types=1);

use Flextype\Flextype;
use Flextype\Entries\Entries;
use Atomastic\Strings\Strings;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test getVersion() method', function () {
    $this->assertTrue(!Strings::create(Flextype::getInstance()->getVersion())->isEmpty());
});

test('test getInstance() method', function () {
    $firstCall = Flextype::getInstance();
    $secondCall = Flextype::getInstance();

    $this->assertInstanceOf(Flextype::class, $firstCall);
    $this->assertSame($firstCall, $secondCall);
});