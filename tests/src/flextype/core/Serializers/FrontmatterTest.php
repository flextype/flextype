<?php

declare(strict_types=1);

test('encode', function () {
    $string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter.md')->get();
    $this->assertEquals($string,
                        serializers()->frontmatter()
                            ->encode(['title' => 'Foo',
                                      'content' => 'Content is here.']));
});

test('decode', function () {
    $this->assertEquals(['title' => 'Foo',
                         'content' => 'Content is here.'],
                        serializers()->frontmatter()
                            ->decode($string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter.md')->get()));
});

test('get cache ID', function () {
    $string = filesystem()->file(ROOT_DIR . '/tests/fixtures/serializers/frontmatter.md')->get();;
    $cache_id = serializers()->frontmatter()
                    ->getCacheID($string);
    $this->assertEquals(32, strlen($cache_id));
    $this->assertNotEquals($string, $cache_id);
});
