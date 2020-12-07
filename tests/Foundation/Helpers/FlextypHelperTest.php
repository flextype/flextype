<?php

declare(strict_types=1);

use Flextype\Foundation\Flextype;
use Flextype\Foundation\Entries\Entries;
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

test('test container() method', function () {
    // get container
    $this->assertInstanceOf(Entries::class, flextype('entries'));

    // set container
    flextype()->container()['foo'] = 'bar';
    $this->assertEquals('bar', flextype('foo'));
});
