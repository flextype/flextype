<?php

declare(strict_types=1);

use function Glowy\Strings\strings;
use function Flextype\serializers;
use function Flextype\registry;

test('encode', function () {
    $this->assertEquals(65, strings(serializers()->phparray()->encode(['title' => 'Foo', 'content' => 'Bar']))->length());

    registry()->set('flextype.settings.serializers.phparray.encode.wrap', false);
    $this->assertEquals(49, strings(serializers()->phparray()->encode(['title' => 'Foo', 'content' => 'Bar']))->length());
});

test('decode', function () {
    $this->assertEquals(['title' => 'Foo', 'content' => 'Bar'], serializers()->phparray()->decode(FLEXTYPE_ROOT_DIR . '/tests/fixtures/serializers/phparray.php'));
});

test('get cache ID', function () {
    $string = strings(serializers()->phparray()->encode(['title' => 'Foo','content' => 'Bar']))->toString();
    $cache_id = serializers()->phparray()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});