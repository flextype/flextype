<?php

declare(strict_types=1);

test('test encode() method', function () {
    $this->assertEquals(35, strings(serializers()->phpcode()->encode(['flextype' => registry()->get("flextype.manifest.version")]))->length());
});

test('test decode() method', function () {
    $this->assertEquals('Flextype', serializers()->phpcode()->decode('registry()->get("flextype.manifest.name")'));
});

test('test getCacheID() method', function () {
    $string = strings(serializers()->phpcode()->encode(['flextype' => registry()->get("flextype.manifest.version")]))->toString();
    $cache_id = serializers()->phparray()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});