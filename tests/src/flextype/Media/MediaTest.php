<?php

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries/media')->ensureExists(0755, true);
    filesystem()->directory(PATH['project'] . '/uploads/media')->ensureExists(0755, true);
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries/media')->delete();
    filesystem()->directory(PATH['project'] . '/uploads/media')->delete();
});

test('test create() method', function () {
    $this->assertTrue(media()->create('foo'));
    $this->assertFalse(media()->create('foo'));
});

test('test move() method', function () {
    $this->assertTrue(media()->create('foo'));
    $this->assertTrue(media()->move('foo', 'bar'));
});

test('test copy() method', function () {
    $this->assertTrue(media()->create('foo'));
    $this->assertTrue(media()->create('bar'));
    $this->assertTrue(media()->copy('foo', 'bar'));
});

test('test delete() method', function () {
    $this->assertTrue(media()->create('foo'));
    $this->assertTrue(media()->delete('foo'));
});