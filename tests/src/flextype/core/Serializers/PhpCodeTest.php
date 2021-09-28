<?php

declare(strict_types=1);

test('encode', function () {
    $this->assertEquals(31, strings(serializers()->phpcode()->encode(['flextype' => registry()->get("flextype.manifest.version")]))->length());

    registry()->set('flextype.settings.serializers.phpcode.encode.wrap', true);
    $this->assertEquals(47, strings(serializers()->phpcode()->encode(['flextype' => registry()->get("flextype.manifest.version")]))->length());
});

test('test encode() throws exception RuntimeException', function (): void {
    serializers()->phpcode()->encode(new Foo());
})->throws(RuntimeException::class);

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

class Foo
{
    public function __serialize()
    {
        throw new RuntimeException('Encoding PhpCode failed');
    }
}