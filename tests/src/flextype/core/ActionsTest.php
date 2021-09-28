<?php

declare(strict_types = 1);

use Flextype\Actions;

test('test getInstance() method', function() {
    $this->assertInstanceOf(Actions::class, Actions::getInstance());
});

test('test registry() helper', function() {
    $this->assertEquals(Actions::getInstance(), registry());
    $this->assertInstanceOf(Actions::class, registry());
});

test('test actions uniqueness', function() {
    $firstCall = Actions::getInstance();
    $secondCall = Actions::getInstance();

    $this->assertInstanceOf(Actions::class, $firstCall);
    $this->assertSame($firstCall, $secondCall);
});

test('test macro() method', function (): void {
    Actions::getInstance()->set('foo', 'bar');

    Actions::macro('customMethod', function() {
        return $this->count();
    });

    $registry = Actions::getInstance();
    $this->assertEquals(1, $registry->customMethod());
});
