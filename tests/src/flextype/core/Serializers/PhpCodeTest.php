<?php

declare(strict_types=1);

test('encode', function () {
    $this->assertEquals(31, strings(serializers()->phpcode()->encode(['flextype' => registry()->get("flextype.manifest.version")]))->length());

    registry()->set('flextype.settings.serializers.phpcode.encode.wrap', true);
    $this->assertEquals(47, strings(serializers()->phpcode()->encode(['flextype' => registry()->get("flextype.manifest.version")]))->length());
});

test('decode', function () {
    $this->assertEquals('Flextype', serializers()->phpcode()->decode('registry()->get("flextype.manifest.name")'));
});

test('get cache ID', function () {
    $string = strings(serializers()->phpcode()->encode(['flextype' => registry()->get("flextype.manifest.version")]))->toString();
    $cache_id = serializers()->phparray()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});