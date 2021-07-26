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

    flextype('entries')->create('foo', ['routable' => true]);
    $routable = flextype('entries')->fetch('foo')['routable'];
    $this->assertTrue($routable);

    flextype('entries')->create('bar', []);
    $routable = flextype('entries')->fetch('bar')['routable'];
    $this->assertTrue($routable);

    flextype('entries')->create('zed', ['routable' => false]);
    $routable = flextype('entries')->fetch('zed')['routable'];
    $this->assertFalse($routable);
});
