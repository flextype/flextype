<?php

declare(strict_types = 1);

use Flextype\Actions;
use function Flextype\actions;

test('test getInstance() method', function() {
    $this->assertInstanceOf(Actions::class, Actions::getInstance());
});

test('test actions() helper', function() {
    $this->assertEquals(Actions::getInstance(), actions());
    $this->assertInstanceOf(Actions::class, actions());
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

    $actions = Actions::getInstance();
    $this->assertEquals(1, $actions->customMethod());
});
