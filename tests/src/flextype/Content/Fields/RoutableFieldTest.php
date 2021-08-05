<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/storage/content')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/storage/content')->delete();
});

test('test RoutableField', function () {
    registry()->set('flextype.settings.cache.enabled', false);

    content()->create('foo', ['routable' => true]);
    $routable = content()->fetch('foo')['routable'];
    $this->assertTrue($routable);

    content()->create('bar', []);
    $routable = content()->fetch('bar')['routable'];
    $this->assertTrue($routable);

    content()->create('zed', ['routable' => false]);
    $routable = content()->fetch('zed')['routable'];
    $this->assertFalse($routable);
});
