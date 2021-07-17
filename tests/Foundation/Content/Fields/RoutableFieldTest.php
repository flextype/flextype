<?php

use Flextype\Component\Filesystem\Filesystem;

beforeEach(function() {
    filesystem()->directory(PATH['project'] . '/entries')->create();
});

afterEach(function (): void {
    filesystem()->directory(PATH['project'] . '/entries')->delete();
});

test('test RoutableField', function () {
    flextype('registry')->set('flextype.settings.cache.enabled', false);

    flextype('content')->create('foo', ['routable' => true]);
    $routable = flextype('content')->fetch('foo')['routable'];
    $this->assertTrue($routable);

    flextype('content')->create('bar', []);
    $routable = flextype('content')->fetch('bar')['routable'];
    $this->assertTrue($routable);

    flextype('content')->create('zed', ['routable' => false]);
    $routable = flextype('content')->fetch('zed')['routable'];
    $this->assertFalse($routable);
});
