<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(FLEXTYPE_PATH_PROJECT . '/entries')->delete();
});

test('RoutableField', function () {
    registry()->set('flextype.settings.cache.enabled', false);

    entries()->create('foo', ['routable' => true]);
    $routable = entries()->fetch('foo')['routable'];
    $this->assertTrue($routable);

    entries()->create('bar', []);
    $routable = entries()->fetch('bar')['routable'];
    $this->assertTrue($routable);

    entries()->create('zed', ['routable' => false]);
    $routable = entries()->fetch('zed')['routable'];
    $this->assertFalse($routable);
});
