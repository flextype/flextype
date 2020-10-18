<?php

declare(strict_types=1);

use Flextype\Foundation\Flextype;
use Flextype\Foundation\Entries\Entries;
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

test('test container() method', function () {
    // get container
    $this->assertInstanceOf(Entries::class, Flextype::getInstance()->container('entries'));

    // set container
    Flextype::getInstance()->container()['foo'] = 'bar';
    $this->assertEquals('bar', Flextype::getInstance()->container('foo'));
});
