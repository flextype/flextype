<?php

declare(strict_types=1);

test('test encode() method', function () {
    $string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/neon.neon')->get();
    $this->assertEquals(trim($string),
                        trim(serializers()->neon()->encode(['hello' => 'world'])));
});

test('test decode() method', function () {
    $this->assertEquals(['hello' => 'world'],
                        serializers()->neon()
                            ->decode($string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/neon.neon')->get()));
});

test('test getCacheID() method', function () {
    $string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/neon.neon')->get();;
    $cache_id = serializers()->neon()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
