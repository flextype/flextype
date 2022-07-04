<?php

declare(strict_types=1);

use function Glowy\Filesystem\filesystem;

test('encode', function () {
    $string = filesystem()->file(FLEXTYPE_ROOT_DIR . '/tests/fixtures/serializers/neon.neon')->get();
    $this->assertEquals(trim("{" . $string . "}"),
                        trim(serializers()->neon()->encode(['hello' => 'world'])));
});

test('decode', function () {
    $this->assertEquals(['hello' => 'world'],
                        serializers()->neon()
                            ->decode($string = filesystem()->file(FLEXTYPE_ROOT_DIR . '/tests/fixtures/serializers/neon.neon')->get()));
});

test('get cache ID', function () {
    $string = filesystem()->file(FLEXTYPE_ROOT_DIR . '/tests/fixtures/serializers/neon.neon')->get();;
    $cache_id = serializers()->neon()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
