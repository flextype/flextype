<?php

declare(strict_types=1);

test('encode', function () {
    $this->assertEquals(67, strings(serializers()->phparray()->encode(['title' => 'Foo', 'content' => 'Bar']))->length());
});

test('decode', function () {
    $this->assertEquals(['title' => 'Foo', 'content' => 'Bar'], serializers()->phparray()->decode(ROOT_DIR . '/tests/fixtures/serializers/phparray.php'));
});

test('get cache ID', function () {
    $string = strings(serializers()->phparray()->encode(['title' => 'Foo','content' => 'Bar']))->toString();
    $cache_id = serializers()->phparray()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});